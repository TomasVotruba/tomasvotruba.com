<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\CleaningCheckList;

use TomasVotruba\CleaningLady\ValueObject\CleaningChecklist;
use TomasVotruba\CleaningLady\ValueObject\CleaningItem;
use TomasVotruba\CleaningLady\ValueObject\CleaningSection;

final class CleaningCheckListFactory
{
    public function create(): CleaningChecklist
    {
        $cleaningSections = [];

        $cleaningSections[] = new CleaningSection('Repository', [
            new CleaningItem(
                'add .editorconfig for all files',
                null,
                'https://tomasvotruba.com/blog/2019/12/23/5-things-i-improve-when-i-get-to-new-repository/'
            ),
            new CleaningItem(
                'apply .editorconfig indent with eclint for all files',
                'https://stackoverflow.com/a/41655803/1348344',
            ),
            new CleaningItem(
                'move vendor to /vendor if somewhere else',
                null,
                'https://tomasvotruba.com/blog/2019/12/23/5-things-i-improve-when-i-get-to-new-repository/'
            ),
            new CleaningItem(
                'apply 1-level directory approach, e.g. configs in /config',
                null,
                'https://tomasvotruba.com/blog/2019/12/23/5-things-i-improve-when-i-get-to-new-repository/'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('composer.json', [
            new CleaningItem(
                'make sure php version is specified',
                null,
                'https://tomasvotruba.com/blog/2019/12/16/8-steps-you-can-make-before-huge-upgrade-to-make-it-faster-cheaper-and-more-stable/#2-explicit-php-version'
            ),
            new CleaningItem(
                'make sure "classmap" is converted to PSR-4',
                null,
                'https://pehapkari.cz/blog/2017/03/02/drop-robot-loader-and-let-composer-deal-with-autoloading'
            ),
            new CleaningItem('make sure "files" is converted to PSR-4'),
            new CleaningItem(
                'add composer scripts for coding standard, PHPStan and Rector',
                'https://blog.martinhujer.cz/have-you-tried-composer-scripts/'
            ),
            new CleaningItem('each composer script has --ansi option, to enable colors in CI'),
            new CleaningItem('move composer scripts options (e.g. --set or --autoload) to config'),
        ]);

        $cleaningSections[] = new CleaningSection('Class naming', [
            new CleaningItem(
                'prefix abstract classes "Abstract"',
                'https://github.com/Slamdunk/phpstan-extensions'
            ),
            new CleaningItem('suffix interface "Interface"', 'https://github.com/Slamdunk/phpstan-extensions'),
        ]);

        $cleaningSections[] = new CleaningSection('Tests', [
            new CleaningItem('make sure tests are in PSR-4, not in PHPUnit autoload magic format'),
            new CleaningItem('run tests in continuous integration'),
            new CleaningItem('make use phpunit.xml is in the root directory'),
            new CleaningItem('make sure the file structure is idential to /src'),
        ]);

        $cleaningSections[] = new CleaningSection('Docblocks', [
            new CleaningItem(
                'remove @author, @covers, @groups',
                'https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/2.16/src/Fixer/Phpdoc/GeneralPhpdocAnnotationRemoveFixer.php'
            ),
            new CleaningItem('remove "class generated ..." spam'),
            new CleaningItem('remove license spam from every file, put into LICENSE file in the root'),
        ]);

        $cleaningSections[] = new CleaningSection('Docker', [
            new CleaningItem('have Dockerfile', 'https://github.com/rectorphp/getrector.org/blob/master/Dockerfile'),
            new CleaningItem(
                'have docker-composer.dist.yml',
                'https://github.com/rectorphp/getrector.org/blob/master/docker-compose.dist.yml'
            ),
            new CleaningItem(
                'have a docs/run_in_docker.md with manual',
                'https://github.com/rectorphp/getrector.org#configure'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('Config', [
            new CleaningItem(
                'move configuration to .env file and load it with symfony/dotenv',
                'https://symfony.com/doc/current/components/dotenv.html'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('Spaghetti', [
            new CleaningItem(
                'make sure functions are converted to Static::calls',
                'https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#functiontostaticmethodrector'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('Coding Standard', [
            new CleaningItem('add Easy Coding Standard', 'https://github.com/symplify/easy-coding-standard'),
            new CleaningItem(
                'add coding standard as standalone CI job',
                'https://github.com/rectorphp/rector/blob/a51551c246a2bbbbabd30ab8512df08e8fa26444/.github/workflows/ecs.yaml'
            ),
            new CleaningItem(
                'migrate current PHP_CodeSniffer to ECS',
                'https://tomasvotruba.com/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard/'
            ),
            new CleaningItem(
                'migrate current PHP CS Fixer to ECS',
                'https://tomasvotruba.com/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard/'
            ),
            new CleaningItem(
                'add "psr12" set',
                'https://github.com/symplify/easy-coding-standard#use-prepared-checker-sets'
            ),
            new CleaningItem(
                'add "common" sets one by one',
                'https://github.com/symplify/easy-coding-standard/tree/master/config/set/common'
            ),
            new CleaningItem('add "php70" set without StrictTypesFixer'),
            new CleaningItem('add "php71" set'),
            new CleaningItem(
                'add 120 chars line length fixer',
                'https://tomasvotruba.com/blog/2018/03/29/new-in-coding-standard-4-long-line-breaks-automated-and-3-legacy-prevention-fixers/'
            ),
            new CleaningItem(
                'change class string to ::class',
                'https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#stringclassnametoclassconstantrector'
            ),
            new CleaningItem(
                'change class::method() string references to ::class',
                'https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#splitstringclassconstanttoclassconstfetchrector'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('PHPStan', [
            new CleaningItem('add PHPStan'),
            new CleaningItem('add level 0'),
            new CleaningItem(
                'add symplify errors formatter for escaped regexs',
                'https://github.com/symplify/phpstan-extensions'
            ),
            new CleaningItem(
                'add PHPStan as standalone CI job',
                'https://github.com/rectorphp/rector/blob/a51551c246a2bbbbabd30ab8512df08e8fa26444/.github/workflows/phpstan.yaml'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('Rector', [
            new CleaningItem(
                'add Rector',
                'https://getrector.org/blog/2020/01/20/how-to-install-rector-despite-composer-conflicts'
            ),
            new CleaningItem(
                'add Rector standalone CI job',
                'https://github.com/symplify/symplify/blob/master/.github/workflows/rector_ci.yaml'
            ),
            new CleaningItem(
                'finalize classes without children',
                'https://github.com/rectorphp/rector/blob/master/docs/AllRectorsOverview.md#finalizeclasseswithoutchildrenrector',
                'https://tomasvotruba.com/blog/2019/01/24/how-to-kill-parents/'
            ),
            new CleaningItem(
                'complete @var types to all your properties',
                'https://tomasvotruba.com/blog/2019/07/29/how-we-completed-thousands-of-missing-var-annotations-in-a-day/'
            ),
            new CleaningItem(
                'remove dead code',
                null,
                'https://tomasvotruba.com/blog/2019/12/09/how-to-get-rid-of-technical-debt-or-what-we-would-have-done-differently-2-years-ago/'
            ),
            new CleaningItem(
                'split grouped property and constant definitions',
                'https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#splitgroupedconstantsandpropertiesrector',
            ),
            new CleaningItem(
                'split grouped use imports definitions',
                'https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md#splitgroupeduseimportsrector',
            ),
        ]);

        $cleaningSections[] = new CleaningSection('Symfony', [
            new CleaningItem(
                'migrate configs to PSR-4 autodiscovery',
                'https://tomasvotruba.com/blog/2018/12/27/how-to-convert-all-your-symfony-service-configs-to-autodiscovery/'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('Nette', [
            new CleaningItem('decouple RouterFactory to own service'),
        ]);

        return new CleaningChecklist($cleaningSections);
    }
}
