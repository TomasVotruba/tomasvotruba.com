<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

final class TweetEntityCompleter
{
    /**
     * @param string[] $tweets
     * @return string[]
     */
    public function completeOriginalUrlsToText(array $tweets): array
    {
        foreach ($tweets as $key => $tweet) {
            $entities = $tweet['entities'];
            if (! count($entities['urls'])) {
                continue;
            }

            foreach ($entities['urls'] as $url) {
                $tweets[$key]['text'] = $fullText = str_replace($url['url'], $url['expanded_url'], $tweet['text']);
            }
        }

        return $tweets;
    }
}
