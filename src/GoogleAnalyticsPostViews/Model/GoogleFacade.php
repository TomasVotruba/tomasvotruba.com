<?php declare(strict_types=1);

namespace TomasVotruba\Website\GoogleAnalyticsPostViews\Model;

use App\Analytics\API;
use Nette\Utils\DateTime;

final class GoogleFacade
{
//    /** @var \App\Analytics\Model\GoogleRepository */
//    private $googleRepository;

//    /** @var GoogleAnalyticsRepository */
//    private $googleAnalyticsRepository;
//
//    /** @var GoogleAnalyticsDataRepository */
//    private $googleAnalyticsDataRepository;

//    /** @var UserRepository */
//    private $userRepository;
//
//    /** @var \App\Admin\Model\CompetitionRepository */
//    private $competitionRepository;
//
//    /** @var \App\Analytics\API */
//    public $api;
//
//    /** @var int */
//    private $userId;
//
//    public function __construct(
////        GoogleRepository $googleRepository,
////        GoogleAnalyticsRepository $googleAnalyticsRepository,
////        GoogleAnalyticsDataRepository $googleAnalyticsDataRepository,
////        UserRepository $userRepository,
////        CompetitionRepository $competitionRepository,
////        API $api
//    )
//    {
////        $this->googleRepository = $googleRepository;
////        $this->googleAnalyticsRepository = $googleAnalyticsRepository;
////        $this->googleAnalyticsDataRepository = $googleAnalyticsDataRepository;
////        $this->userRepository = $userRepository;
////        $this->competitionRepository = $competitionRepository;
//        $this->api = $api;
//    }

    /**
     * Returns user's access token
     * @param $userId null|int
     * @return bool
     */
    public function getAccessToken($userId = NULL)
    {
        if(!is_null($userId))
            $this->userId = $userId;

        $tokens = $this->get($this->userId);
        if (!$tokens)
            return FALSE;

        return $tokens->accessToken;
    }

    public function getJsonTokens()
    {
        $tokens = $this->get($this->userId);
        if (!$tokens)
            return FALSE;

        return $tokens->raw;
    }

    /**
     * Saves user's tokens
     * @param $tokens
     * @param $userId
     * @return \MYPS\ORM\ActiveRow
     */
    public function saveTokens($tokens, $userId)
    {
        $data = json_decode($tokens, TRUE);

        $expiration = new DateTime();
        $expiration->modify('+' . $data['expires_in'] . ' seconds');

        $values = [
            'accessToken' => $data['access_token'],
            'refreshToken' => $data['refresh_token'],
            'expiresIn' => $expiration,
            'user_id' => $userId,
            'raw' => $tokens
        ];

        $google = $this->googleRepository->create($values);

        $this->userRepository->update($userId, [
            'google_id' => $google
        ]);

        return $google;
    }

    /**
     * Return collection of google analytics accounts
     * @param $userId
     * @return array
     */
    public function getAccountList($userId)
    {
        try {
            $this->setTokens($userId);

        } catch (\Google_Auth_Exception $e) {
            $this->revokeToken($userId);
            return FALSE;
        }

        $result = [];

        $service = new \Google_Service_Analytics($this->api->client);

        // request user accounts
        $accounts = $service->management_accountSummaries->listManagementAccountSummaries();

        foreach ($accounts->getItems() as $account) {
            $result[$account['id']]['name'] = $account['name'];

            foreach ($account->getWebProperties() as $wp) {

                $result[$account['id']]['properties'][$wp['id']]['name'] = $wp['name'];

                $views = $wp->getProfiles();
                if (!is_null($views)) {
                    foreach ($wp->getProfiles() as $view) {
                        $result[$account['id']]['properties'][$wp['id']]['profiles'][$view['id']] = $view['name'];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns google analytics users in array
     * @param $userId
     * @param $profileId
     * @param $from
     * @param $to
     * @return array
     */
    public function getGoogleAnalyticsUsers($userId, $profileId, $from, $to)
    {
        $this->setTokens($userId);

        return $this->api->getUsers($profileId, $from, $to);
    }

    /**
     * Set token and grant access
     * If token expires, get new access token
     * @param $userId
     * @return FALSE
     */
    private function setTokens($userId)
    {
        $this->userId = $userId;

        // Check expiration time
        $row = $this->get($userId);

        // Expired - get new access token
        if ($row->expiresIn < new DateTime()) {
            $tokens = $this->api->refreshToken($row->refreshToken);
            $data = json_decode($tokens, TRUE);

            $expiration = new DateTime();
            $expiration->modify('+' . $data['expires_in'] . ' seconds');

            $values = [
                'accessToken' => $data['access_token'],
                'expiresIn' => $expiration,
                'raw' => $tokens
            ];

            $this->googleRepository->update($row->id, $values);
        }

        $this->api->setTokens($this->getJsonTokens());
    }

    public function getGoogleAnalyticsData($analyticsId, $period = '-1 month')
    {
        return $this->googleAnalyticsDataRepository
            ->findAll()
            ->where('google_analytics_id', $analyticsId)
            ->where('date >= ?', new DateTime($period));
    }

    public function getGoogleAnalyticsDataFromTo($analyticsId, $dateFrom, $dateTo)
    {
        return $this->googleAnalyticsDataRepository
            ->findAll()
            ->where('google_analytics_id', $analyticsId)
            ->where('date >= ?', $dateFrom)
            ->where('date <= ?', $dateTo);
    }

    /**
     * Updates today's google analytics data.
     * If new day set new date and update final count of the last day.
     */
    public function fetchGoogleAnalyticsData()
    {
        try {
            $now = new DateTime();

            foreach ($this->competitionRepository->findAll()->where([
                'DATE(beginAt) <= ?' => $now->format('Y-n-d'),
                'DATE(endAt) >= ?' => $now->format('Y-n-d')
            ]) as $competition) {

                if (!is_null($competition->google_analytics_id) && !is_null($competition->google_analytics->profileId)) {

                    $today = new DateTime();

                    // Check if exists today's record
                    $row = $this->googleAnalyticsDataRepository
                        ->findAll()
                        ->where('google_analytics_id', $competition->google_analytics->id)
                        ->where('DATE(date)', $today->format('Y-n-d'))
                        ->fetch();

                    if (!$row) {
                        $yesterday = new DateTime('-1 day');
                        // If not, fetch final yesterday's count
                        $this->updateGoogleAnalyticsData($competition->google_analytics, $yesterday, $yesterday);

                        // And create today's
                        $this->saveGoogleAnalyticsData($competition->google_analytics, 'now');

                    } else {
                        $this->updateGoogleAnalyticsData($competition->google_analytics, $today, $today);
                    }
                }
            }

        } catch (\Google_Auth_Exception $e) {
            /**
             * This exception can be thrown when token expired.
             * Cron is called every hour, token expiration is one hour.
             * Can happened that access token has expired. In this case wait to next call when access token is OK.
             */
        }
    }

    /**
     * Return if competition is connected to GA
     * @param $competition
     * @return bool
     */
    public function isConnected($competition)
    {
        return !is_null($competition->google_analytics_id)
        && $this->getGoogleAnalyticsData($competition->google_analytics->id)->count('id')
        && !is_null($competition->google_analytics->profileId);
    }
}