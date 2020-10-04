<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TomasVotruba\Website\ValueObject\Option as OptionAlias;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(OptionAlias::FRAMEWORKS_VENDOR_TO_NAME, [
        'nette' => 'Nette',
        'symfony' => 'Symfony',
        'illuminate' => 'Laravel',
        'cakephp' => 'CakePHP',
        'zendframework' => 'Zend',
        'yiisoft' => 'Yii',
        'doctrine' => 'Doctrine',
    ]);

    $parameters->set(OptionAlias::EXCLUDED_FRAMEWORK_PACKAGES, [
        'symfony/security-guard',
        'symfony/security-http',
        'symfony/security-csrf',
        'symfony/lts',
        'symfony/thanks',
        'symfony/polyfill',
        'symfony/polyfill-*',
        'symfony/*-pack',
        'symfony/*-bundle',
        'symfony/class-loader',
        'symfony/assetic-bundle',
        'symfony/locale',
        'symfony/icu',
        'symfony/swiftmailer-bridge',
        'illuminate/html',
        'doctrine/*-module',
        'doctrine/*-bundle',
        'doctrine/static-website-generator',
        'doctrine/doctrine1',
        'doctrine/coding-standard',
        'cakephp/elastic-search',
        'cakephp/acl',
        'yiisoft/yii2-apidoc',
        'zendframework/zend-expressive-*',
        'zendframework/skeleton-application',
        'zendframework/zend-config-*',
        'zendframework/zend-developer-tools',
        '*/contracts',
        'symfony/*-contracts',
        'symfony/symfony1',
        'symfony/symfony-demo',
        'symfony/skeleton',
        'symfony/requirements-checker',
        'symfony/framework-standard-edition',
        'symfony/force-lowest',
        'symfony/image-fixtures',
        'symfony/*-bridge',
        'symfony/symfony-installer',
        'nette/sandbox',
        'nette/nette-minified',
        'nette/deprecated',
        'nette/extras',
        'nette/coding-standard',
        'nette/code-checker',
        'nette/addon-installer',
        'nette/type-fixer',
        'nette/web-project',
    ]);
};
