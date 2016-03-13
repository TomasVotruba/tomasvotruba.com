<?php

namespace TomasVotruba\SculpinTranslationBundle\Translation;

final class MessageAnalyzer
{
    /**
     * @param string $message
     * @return array
     */
    public function extractDomainFromMessage($message)
    {
        if (strpos($message, '.') !== false && strpos($message, ' ') === false) {
            list($domain, $message) = explode('.', $message, 2);
        } else {
            $domain = 'messages';
        }

        return [$domain, $message];
    }
}
