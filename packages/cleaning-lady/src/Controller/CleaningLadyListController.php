<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\CleaningLady\ValueObject\CleaningChecklist;
use TomasVotruba\CleaningLady\ValueObject\CleaningItem;
use TomasVotruba\CleaningLady\ValueObject\CleaningSection;

final class CleaningLadyListController extends AbstractController
{
    /**
     * @Route(path="cleaning-lady-list", name="cleaning_lady")
     */
    public function __invoke(): Response
    {
        return $this->render('cleaning_lady_list.twig', [
            'title' => 'Cleaning Lady list',
            'checklist' => $this->createCleaningChecklist(),
        ]);
    }

    private function createCleaningChecklist(): CleaningChecklist
    {
        $cleaningSections = [];

        $cleaningSections[] = new CleaningSection('Repository', [
            new CleaningItem(
                'add .editorconfig for all files',
                null,
                'https://www.tomasvotruba.com/blog/2019/12/23/5-things-i-improve-when-i-get-to-new-repository/'
            ),
            new CleaningItem(
                'move vendor to /vendor if somewhere else',
                null,
                'https://www.tomasvotruba.com/blog/2019/12/23/5-things-i-improve-when-i-get-to-new-repository/'
            ),
            new CleaningItem(
                'apply 1-level directory approach, e.g. configs in /config',
                null,
                'https://www.tomasvotruba.com/blog/2019/12/23/5-things-i-improve-when-i-get-to-new-repository/'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('composer.json', [
            new CleaningItem(
                'make sure php version is specified',
                null,
                'https://www.tomasvotruba.com/blog/2019/12/16/8-steps-you-can-make-before-huge-upgrade-to-make-it-faster-cheaper-and-more-stable/#2-explicit-php-version'
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
            new CleaningItem('move composer scripts options (e.g. --set or --autoload) to config'),
        ]);

        $cleaningSections[] = new CleaningSection('Spaghetti', [
            new CleaningItem(
                'make sure functions are converted to static method',
                'https://github.com/rectorphp/rector/issues/3101'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('Coding Standard', [
            new CleaningItem(
                'add Easy Coding Standard',
                'https://www.tomasvotruba.com/blog/2020/01/20/introducing-phar-for-easy-coding-standard/'
            ),
            new CleaningItem(
                'add coding standard as standalone CI job',
                'https://github.com/rectorphp/rector/blob/a51551c246a2bbbbabd30ab8512df08e8fa26444/.github/workflows/ecs.yaml'
            ),
            new CleaningItem(
                'add "psr12" set',
                'https://github.com/symplify/easy-coding-standard#use-prepared-checker-sets'
            ),
            new CleaningItem(
                'add "common" sets one by one',
                'https://github.com/symplify/easy-coding-standard/tree/master/config/set/common'
            ),
            new CleaningItem('add "php70" set without StrictTypeFxer', ''),
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
                'https://www.tomasvotruba.com/blog/2019/01/24/how-to-kill-parents/'
            ),
            new CleaningItem(
                'complete @var types to all your properties',
                'https://www.tomasvotruba.com/blog/2019/07/29/how-we-completed-thousands-of-missing-var-annotations-in-a-day/'
            ),
            new CleaningItem(
                'remove dead code',
                null,
                'https://www.tomasvotruba.com/blog/2019/12/09/how-to-get-rid-of-technical-debt-or-what-we-would-have-done-differently-2-years-ago/'
            ),
        ]);

        $cleaningSections[] = new CleaningSection('Symfony', [
            new CleaningItem(
                'migrate configs to PSR-4 autodiscovery',
                'https://www.tomasvotruba.com/blog/2018/12/27/how-to-convert-all-your-symfony-service-configs-to-autodiscovery/'
            ),
        ]);

        return new CleaningChecklist($cleaningSections);
    }
}
