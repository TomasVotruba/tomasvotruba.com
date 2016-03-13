<?php

namespace TomasVotruba\SculpinTranslationBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;
use TomasVotruba\SculpinTranslationBundle\Translation\MessageAnalyzer;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFilter;

final class SculpinTranslationExtension extends Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var MessageAnalyzer
     */
    private $messageAnalyzer;

    public function __construct(TranslatorInterface $translator, MessageAnalyzer $messageAnalyzer)
    {
        $this->translator = $translator;
        $this->messageAnalyzer = $messageAnalyzer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('trans', function ($message, $locale) {
                return $this->translate($message, $locale);
            })
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * @param string $message
     * @param string $locale
     * @return string
     */
    private function translate($message, $locale)
    {
        list($domain, $id) = $this->messageAnalyzer->extractDomainFromMessage($message);

        return $this->translator->trans($id, [], $domain, $locale ?: $this->translator->getLocale());
    }
}
