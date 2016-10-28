<?php

namespace TomasVotruba\SculpinTranslationBundle\Translation;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use TomasVotruba\SculpinTranslationBundle\Filesystem\ResourceFinder;

final class TranslatorFactory
{
    /**
     * @var string
     */
    private $translationDir;

    /**
     * @var ResourceFinder
     */
    private $resourceFinder;

    /**
     * @param string $translationDir
     */
    public function __construct($translationDir, ResourceFinder $resourceFinder)
    {
        $this->translationDir = $translationDir;
        $this->resourceFinder = $resourceFinder;
    }

    /**
     * @return Translator
     */
    public function create()
    {
        $translator = new Translator(null);
        $translator->addLoader('yml', new YamlFileLoader());
        $translator->setFallbackLocales(['cs']);

        $translator = $this->addResourcesToTranslator($translator);

        return $translator;
    }

    /**
     * @return TranslatorInterface
     */
    private function addResourcesToTranslator(Translator $translator)
    {
        foreach ($this->resourceFinder->findInDirectory($this->translationDir) as $resource) {
            $translator->addResource(
                $resource['format'],
                $resource['pathname'],
                $resource['locale'],
                $resource['domain']
            );
        }

        return $translator;
    }
}
