<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TomasVotruba\Website\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::TALKS, [
        [
            'title' => 'Instant Upgrades with Rector (and variations)',
            'anchor' => 'rector',
            'demo_link' => 'https://github.com/rectorphp/demo',
            'video_link' => 'https://www.youtube.com/watch?v=gQWQA-GZxXc',
            'post_link' => '/blog/2018/02/19/rector-part-1-what-and-how/',
            'events' => [
                [
                    'name' => 'Friends of PHP Prague',
                    'location' => 'Prague, Czech Republic',
                    'url' => 'https://www.facebook.com/pehapkari/videos/399224180756304',
                    'date' => '2019-10-17',
                ], [
                    'name' => 'PHP SW',
                    'location' => 'Bristol, UK',
                    'url' => 'https://www.meetup.com/php-sw/events/265188280/',
                    'date' => '2019-10-08',
                ], [
                    'name' => 'Web Summer Camp',
                    'location' => 'Rovinj, Croatia',
                    'url' => 'https://2019.websummercamp.com/how-to-make-legacy-refactoring-fun-again-from-months-to-days',
                    'date' => '2019-08-29',
                ], [
                    'name' => 'Dutch PHP Conference',
                    'location' => 'Amsterdam, Netherlands',
                    'url' => 'https://www.phpconference.nl/',
                    'date' => '2019-06-08',
                ], [
                    'name' => 'PHP fwdays',
                    'location' => 'Kiev, Ukraine',
                    'url' => 'https://fwdays.com/en/event/php-fwdays-2019',
                    'date' => '2019-06-01',
                ], [
                    'name' => 'PHP Russia',
                    'location' => 'Moscow, Russia',
                    'url' => 'https://phprussia.ru/2019',
                    'date' => '2019-05-17',
                ], [
                    'name' => 'phpDay 2019',
                    'location' => 'Verona, Italy',
                    'url' => 'https://2019.phpday.it/',
                    'date' => '2019-05',
                ], [
                    'name' => 'Friends of PHP Brno',
                    'location' => 'The Czech Republic',
                    'date' => '2019-03',
                    'url' => 'https://www.facebook.com/events/415328609225062/',
                ], [
                    'name' => 'Dresden PHP UG',
                    'location' => 'Germany',
                    'date' => '2019-02',
                    'url' => 'https://www.meetup.com/PHP-USERGROUP-DRESDEN/events/257775077/',
                ], [
                    'name' => 'Friends of PHP Brno',
                    'location' => 'The Czech Republic',
                    'date' => '2019-02',
                    'url' => 'https://www.meetup.com/Pehapkari-Brno/events/258960849/',
                ], [
                    'name' => 'Vienna PHP',
                    'location' => 'Austria',
                    'date' => '2019-02',
                    'url' => 'https://www.meetup.com/viennaphp/events/ncmvwpyzdbcc/',
                ], [
                    'name' => 'Friends of PHP Vysočina',
                    'location' => 'The Czech Republic',
                    'date' => '2019-02',
                    'url' => 'https://www.facebook.com/events/342162879917136/',
                ], [
                    'name' => 'PHP Asia',
                    'location' => 'Singapore',
                    'date' => '2018-09',
                    'url' => 'https://2018.phpconf.asia/',
                ], [
                    'name' => 'Symfony UG',
                    'location' => 'Berlin, Germany',
                    'date' => '2018-08',
                    'url' => 'https://www.meetup.com/sfugberlin/events/253573567',
                ], [
                    'name' => 'Vienna PHP',
                    'location' => 'Austria',
                    'date' => '2018-09',
                    'url' => 'https://www.meetup.com/viennaphp/events/ncmvwpyxmbbc/',
                ], [
                    'name' => 'Vienna PHP',
                    'location' => 'Austria',
                    'date' => '2018-06',
                    'url' => 'https://www.meetup.com/viennaphp/events/djtpjpyxjbcc/',
                ], [
                    'name' => 'Friends of PHP Pardubice',
                    'location' => 'Pardubice, The Czech Republic',
                    'date' => '2018-06',
                    'url' => 'https://www.facebook.com/events/1783858791700944/',
                ], [
                    'name' => 'Berlin PHP UG',
                    'location' => 'Germany',
                    'date' => '2018-06',
                    'url' => 'http://www.bephpug.de/2018/06/05/june.html',
                ], [
                    'name' => 'PHPLive',
                    'location' => 'The Czech Republic & Slovakia',
                    'date' => '2018-04',
                    'url' => 'https://www.phplive.cz',
                ], [
                    'name' => 'PHP Central Europe Conference',
                    'location' => 'Poland',
                    'date' => '2017-11',
                    'url' => 'https://2017.phpce.eu',
                ],
            ],
        ],
        [
            'title' => 'How to Setup Coding Standards under 5 minutes',
            'anchor' => 'ecs',
            'related_post' => '/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/',
            'video_url' => 'https://www.facebook.com/pehapkari/videos/vl.1769747516670517/1341201322596341/?type=1',
            'events' => [
                [
                    'name' => 'PHP Central Europe Conference',
                    'location' => 'Poland',
                    'date' => '2017-11',
                    'url' => 'https://2017.phpce.eu',
                ], [
                    'name' => 'Vienna PHP',
                    'location' => 'Austria',
                    'date' => '2017-05',
                    'url' => 'https://www.meetup.com/viennaphp/events/237296390',
                ], [
                    'name' => 'Dresden PHP UG',
                    'location' => 'Germany',
                    'date' => '2017-04',
                    'url' => 'https://www.meetup.com/PHP-USERGROUP-DRESDEN/events/238473452',
                ], [
                    'name' => 'Nette Camp',
                    'location' => 'The Czech Republic',
                    'date' => '2017-08',
                    'url' => 'https://nettecamp.cz',
                ],
            ],
        ],
        [
            'title' => 'How to Run Monorepo under 10 minutes',
            'anchor' => 'monorepo',
            'related_post' => '/blog/2018/10/08/new-in-symplify-5-create-merge-and-split-monorepo-with-1-command/',
            'demo_link' => 'https://github.com/TomasVotruba/monorepo-demo',
            'video_link' => 'https://slideslive.com/38911637/jak-rozjet-monorepo-za-10-minut',
            'events' => [
                [
                    'name' => 'Developer Day 2018',
                    'location' => 'The Czech Republic',
                    'date' => '2018-11',
                    'url' => 'https://www.hubbr.cz/udalosti/events/developer-day-2018',
                ], [
                    'name' => 'Friends of PHP Prague',
                    'location' => 'The Czech Republic',
                    'date' => '2018-09',
                    'url' => 'https://www.meetup.com/friends-of-php-prague/events/254388973/',
                ],
            ],
        ],
    ]);
};
