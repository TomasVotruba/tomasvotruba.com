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

            $tweets[$key]['text'] = $this->replaceShortUrlsWithOriginalOnes($entities, $tweet);
        }

        return $tweets;
    }

    /**
     * @param mixed[] $entities
     */
    private function replaceShortUrlsWithOriginalOnes(array $entities, string $tweet): string
    {
        foreach ($entities['urls'] as $url) {
            $tweet['text'] = str_replace($url['url'], $url['expanded_url'], $tweet['text']);
        }

        return $tweet;
    }
}
