<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TomasVotruba\GithubContributorsThanker\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(
        Option::CONTRIBUTORS,
        [[
            'name' => 'vrbata',
            'url' => 'https://github.com/vrbata',
            'photo' => 'https://avatars1.githubusercontent.com/u/8536429?v=4',
            'contribution_count' => 13,
        ], [
            'name' => 'Gappa',
            'url' => 'https://github.com/Gappa',
            'photo' => 'https://avatars3.githubusercontent.com/u/749981?v=4',
            'contribution_count' => 12,
        ], [
            'name' => 'enumag',
            'url' => 'https://github.com/enumag',
            'photo' => 'https://avatars3.githubusercontent.com/u/539462?v=4',
            'contribution_count' => 10,
        ], [
            'name' => 'mssimi',
            'url' => 'https://github.com/mssimi',
            'photo' => 'https://avatars0.githubusercontent.com/u/16163762?v=4',
            'contribution_count' => 10,
        ], [
            'name' => 'tavy315',
            'url' => 'https://github.com/tavy315',
            'photo' => 'https://avatars2.githubusercontent.com/u/6184412?v=4',
            'contribution_count' => 10,
        ], [
            'name' => 'ondrejmirtes',
            'url' => 'https://github.com/ondrejmirtes',
            'photo' => 'https://avatars0.githubusercontent.com/u/104888?v=4',
            'contribution_count' => 7,
        ], [
            'name' => 'f3l1x',
            'url' => 'https://github.com/f3l1x',
            'photo' => 'https://avatars1.githubusercontent.com/u/538058?v=4',
            'contribution_count' => 6,
        ], [
            'name' => 'JanMikes',
            'url' => 'https://github.com/JanMikes',
            'photo' => 'https://avatars3.githubusercontent.com/u/3995003?v=4',
            'contribution_count' => 6,
        ], [
            'name' => 'ikvasnica',
            'url' => 'https://github.com/ikvasnica',
            'photo' => 'https://avatars3.githubusercontent.com/u/4759802?v=4',
            'contribution_count' => 5,
        ], [
            'name' => 'crazko',
            'url' => 'https://github.com/crazko',
            'photo' => 'https://avatars2.githubusercontent.com/u/1255989?v=4',
            'contribution_count' => 5,
        ], [
            'name' => 'freiondrej',
            'url' => 'https://github.com/freiondrej',
            'photo' => 'https://avatars2.githubusercontent.com/u/37588173?v=4',
            'contribution_count' => 5,
        ], [
            'name' => 'juananruiz',
            'url' => 'https://github.com/juananruiz',
            'photo' => 'https://avatars1.githubusercontent.com/u/211510?v=4',
            'contribution_count' => 5,
        ], [
            'name' => 'Maxell92',
            'url' => 'https://github.com/Maxell92',
            'photo' => 'https://avatars3.githubusercontent.com/u/1649279?v=4',
            'contribution_count' => 4,
        ], [
            'name' => 'tiso',
            'url' => 'https://github.com/tiso',
            'photo' => 'https://avatars3.githubusercontent.com/u/920949?v=4',
            'contribution_count' => 4,
        ], [
            'name' => 'escopecz',
            'url' => 'https://github.com/escopecz',
            'photo' => 'https://avatars2.githubusercontent.com/u/1235442?v=4',
            'contribution_count' => 4,
        ], [
            'name' => 'uestla',
            'url' => 'https://github.com/uestla',
            'photo' => 'https://avatars1.githubusercontent.com/u/373888?v=4',
            'contribution_count' => 3,
        ], [
            'name' => 'JanGalek',
            'url' => 'https://github.com/JanGalek',
            'photo' => 'https://avatars2.githubusercontent.com/u/13171542?v=4',
            'contribution_count' => 3,
        ], [
            'name' => 'javiereguiluz',
            'url' => 'https://github.com/javiereguiluz',
            'photo' => 'https://avatars3.githubusercontent.com/u/73419?v=4',
            'contribution_count' => 3,
        ], [
            'name' => 'martinahynkova',
            'url' => 'https://github.com/martinahynkova',
            'photo' => 'https://avatars1.githubusercontent.com/u/24881512?v=4',
            'contribution_count' => 3,
        ], [
            'name' => 'carusogabriel',
            'url' => 'https://github.com/carusogabriel',
            'photo' => 'https://avatars3.githubusercontent.com/u/16328050?v=4',
            'contribution_count' => 3,
        ], [
            'name' => 'petrofcikmatus',
            'url' => 'https://github.com/petrofcikmatus',
            'photo' => 'https://avatars1.githubusercontent.com/u/11237717?v=4',
            'contribution_count' => 3,
        ], [
            'name' => 'andrewmy',
            'url' => 'https://github.com/andrewmy',
            'photo' => 'https://avatars2.githubusercontent.com/u/715595?v=4',
            'contribution_count' => 3,
        ], [
            'name' => 'cafferata',
            'url' => 'https://github.com/cafferata',
            'photo' => 'https://avatars2.githubusercontent.com/u/1150425?v=4',
            'contribution_count' => 3,
        ], [
            'name' => 'tomasfejfar',
            'url' => 'https://github.com/tomasfejfar',
            'photo' => 'https://avatars0.githubusercontent.com/u/642928?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'kbond',
            'url' => 'https://github.com/kbond',
            'photo' => 'https://avatars0.githubusercontent.com/u/127811?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'robertfausk',
            'url' => 'https://github.com/robertfausk',
            'photo' => 'https://avatars0.githubusercontent.com/u/1651297?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'harikt',
            'url' => 'https://github.com/harikt',
            'photo' => 'https://avatars3.githubusercontent.com/u/120454?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'clobee',
            'url' => 'https://github.com/clobee',
            'photo' => 'https://avatars2.githubusercontent.com/u/3452074?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'praczynski',
            'url' => 'https://github.com/praczynski',
            'photo' => 'https://avatars1.githubusercontent.com/u/455039?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'garas',
            'url' => 'https://github.com/garas',
            'photo' => 'https://avatars3.githubusercontent.com/u/2265694?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'lchrusciel',
            'url' => 'https://github.com/lchrusciel',
            'photo' => 'https://avatars2.githubusercontent.com/u/6213903?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'GenieTim',
            'url' => 'https://github.com/GenieTim',
            'photo' => 'https://avatars0.githubusercontent.com/u/8596965?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'fruitl00p',
            'url' => 'https://github.com/fruitl00p',
            'photo' => 'https://avatars1.githubusercontent.com/u/1492861?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'natepage',
            'url' => 'https://github.com/natepage',
            'photo' => 'https://avatars0.githubusercontent.com/u/11576446?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'TomPavelec',
            'url' => 'https://github.com/TomPavelec',
            'photo' => 'https://avatars2.githubusercontent.com/u/46928405?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'EduardoRT',
            'url' => 'https://github.com/EduardoRT',
            'photo' => 'https://avatars0.githubusercontent.com/u/1114422?v=4',
            'contribution_count' => 2,
        ], [
            'name' => 'hranicka',
            'url' => 'https://github.com/hranicka',
            'photo' => 'https://avatars1.githubusercontent.com/u/3034538?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'honzagula',
            'url' => 'https://github.com/honzagula',
            'photo' => 'https://avatars3.githubusercontent.com/u/15079308?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'tacman',
            'url' => 'https://github.com/tacman',
            'photo' => 'https://avatars2.githubusercontent.com/u/619585?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'PetrHeinz',
            'url' => 'https://github.com/PetrHeinz',
            'photo' => 'https://avatars1.githubusercontent.com/u/10008612?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'keradus',
            'url' => 'https://github.com/keradus',
            'photo' => 'https://avatars1.githubusercontent.com/u/2716794?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'integer',
            'url' => 'https://github.com/integer',
            'photo' => 'https://avatars0.githubusercontent.com/u/160891?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'sustmi',
            'url' => 'https://github.com/sustmi',
            'photo' => 'https://avatars2.githubusercontent.com/u/885946?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'tommy-muehle',
            'url' => 'https://github.com/tommy-muehle',
            'photo' => 'https://avatars0.githubusercontent.com/u/1351840?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'Wirone',
            'url' => 'https://github.com/Wirone',
            'photo' => 'https://avatars2.githubusercontent.com/u/600668?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'muglug',
            'url' => 'https://github.com/muglug',
            'photo' => 'https://avatars0.githubusercontent.com/u/2292638?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'meridius',
            'url' => 'https://github.com/meridius',
            'photo' => 'https://avatars1.githubusercontent.com/u/372431?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'gavec',
            'url' => 'https://github.com/gavec',
            'photo' => 'https://avatars1.githubusercontent.com/u/4173632?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'nufue',
            'url' => 'https://github.com/nufue',
            'photo' => 'https://avatars0.githubusercontent.com/u/8158773?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'fabwu',
            'url' => 'https://github.com/fabwu',
            'photo' => 'https://avatars2.githubusercontent.com/u/6050639?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'danielsdeboer',
            'url' => 'https://github.com/danielsdeboer',
            'photo' => 'https://avatars2.githubusercontent.com/u/13170241?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'jlttt',
            'url' => 'https://github.com/jlttt',
            'photo' => 'https://avatars1.githubusercontent.com/u/22560547?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'enid-h-williams',
            'url' => 'https://github.com/enid-h-williams',
            'photo' => 'https://avatars2.githubusercontent.com/u/1844329?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'artemevsin',
            'url' => 'https://github.com/artemevsin',
            'photo' => 'https://avatars1.githubusercontent.com/u/12592377?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'kapilsharma',
            'url' => 'https://github.com/kapilsharma',
            'photo' => 'https://avatars1.githubusercontent.com/u/2530564?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'Vesninandrey',
            'url' => 'https://github.com/Vesninandrey',
            'photo' => 'https://avatars2.githubusercontent.com/u/6872815?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'tnorthcutt',
            'url' => 'https://github.com/tnorthcutt',
            'photo' => 'https://avatars3.githubusercontent.com/u/796639?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'chapeupreto',
            'url' => 'https://github.com/chapeupreto',
            'photo' => 'https://avatars3.githubusercontent.com/u/834048?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'EdaCZ',
            'url' => 'https://github.com/EdaCZ',
            'photo' => 'https://avatars3.githubusercontent.com/u/1671637?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'kralmichal',
            'url' => 'https://github.com/kralmichal',
            'photo' => 'https://avatars2.githubusercontent.com/u/1733478?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'bendavies',
            'url' => 'https://github.com/bendavies',
            'photo' => 'https://avatars3.githubusercontent.com/u/625392?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'avaneeshsingh',
            'url' => 'https://github.com/avaneeshsingh',
            'photo' => 'https://avatars2.githubusercontent.com/u/5363929?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'pdelre',
            'url' => 'https://github.com/pdelre',
            'photo' => 'https://avatars0.githubusercontent.com/u/1379248?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'Baystef',
            'url' => 'https://github.com/Baystef',
            'photo' => 'https://avatars2.githubusercontent.com/u/36106823?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'chalasr',
            'url' => 'https://github.com/chalasr',
            'photo' => 'https://avatars0.githubusercontent.com/u/7502063?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'hrvoj3e',
            'url' => 'https://github.com/hrvoj3e',
            'photo' => 'https://avatars0.githubusercontent.com/u/4988133?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'jDolba',
            'url' => 'https://github.com/jDolba',
            'photo' => 'https://avatars3.githubusercontent.com/u/2221925?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'maccath',
            'url' => 'https://github.com/maccath',
            'photo' => 'https://avatars3.githubusercontent.com/u/904427?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'Wojciechem',
            'url' => 'https://github.com/Wojciechem',
            'photo' => 'https://avatars3.githubusercontent.com/u/4303141?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'zendiik',
            'url' => 'https://github.com/zendiik',
            'photo' => 'https://avatars2.githubusercontent.com/u/10212768?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'COil',
            'url' => 'https://github.com/COil',
            'photo' => 'https://avatars0.githubusercontent.com/u/177844?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'pgl',
            'url' => 'https://github.com/pgl',
            'photo' => 'https://avatars0.githubusercontent.com/u/1181139?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'leksinteractive',
            'url' => 'https://github.com/leksinteractive',
            'photo' => 'https://avatars1.githubusercontent.com/u/3906239?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'samdark',
            'url' => 'https://github.com/samdark',
            'photo' => 'https://avatars2.githubusercontent.com/u/47294?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'voku',
            'url' => 'https://github.com/voku',
            'photo' => 'https://avatars0.githubusercontent.com/u/264695?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'ravage84',
            'url' => 'https://github.com/ravage84',
            'photo' => 'https://avatars1.githubusercontent.com/u/625761?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'r-amos',
            'url' => 'https://github.com/r-amos',
            'photo' => 'https://avatars3.githubusercontent.com/u/23461699?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'hpneo',
            'url' => 'https://github.com/hpneo',
            'photo' => 'https://avatars2.githubusercontent.com/u/611699?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'imliam',
            'url' => 'https://github.com/imliam',
            'photo' => 'https://avatars0.githubusercontent.com/u/4326337?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'Lyssal',
            'url' => 'https://github.com/Lyssal',
            'photo' => 'https://avatars1.githubusercontent.com/u/10855303?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'hagiosofori',
            'url' => 'https://github.com/hagiosofori',
            'photo' => 'https://avatars3.githubusercontent.com/u/11246351?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'DominikTo',
            'url' => 'https://github.com/DominikTo',
            'photo' => 'https://avatars2.githubusercontent.com/u/1384635?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'brooksvb',
            'url' => 'https://github.com/brooksvb',
            'photo' => 'https://avatars3.githubusercontent.com/u/14019044?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'Nsbx',
            'url' => 'https://github.com/Nsbx',
            'photo' => 'https://avatars0.githubusercontent.com/u/8930930?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'shaal',
            'url' => 'https://github.com/shaal',
            'photo' => 'https://avatars2.githubusercontent.com/u/22901?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'Mandy91',
            'url' => 'https://github.com/Mandy91',
            'photo' => 'https://avatars2.githubusercontent.com/u/69206532?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'DeyV',
            'url' => 'https://github.com/DeyV',
            'photo' => 'https://avatars0.githubusercontent.com/u/311626?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'LC43',
            'url' => 'https://github.com/LC43',
            'photo' => 'https://avatars2.githubusercontent.com/u/48201?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'OskarStark',
            'url' => 'https://github.com/OskarStark',
            'photo' => 'https://avatars0.githubusercontent.com/u/995707?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'honzajavorek',
            'url' => 'https://github.com/honzajavorek',
            'photo' => 'https://avatars0.githubusercontent.com/u/283441?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'staabm',
            'url' => 'https://github.com/staabm',
            'photo' => 'https://avatars2.githubusercontent.com/u/120441?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'adaamz',
            'url' => 'https://github.com/adaamz',
            'photo' => 'https://avatars0.githubusercontent.com/u/4347332?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'andrew-demb',
            'url' => 'https://github.com/andrew-demb',
            'photo' => 'https://avatars2.githubusercontent.com/u/12499813?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'AegirLeet',
            'url' => 'https://github.com/AegirLeet',
            'photo' => 'https://avatars3.githubusercontent.com/u/33277331?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'ElGovanni',
            'url' => 'https://github.com/ElGovanni',
            'photo' => 'https://avatars0.githubusercontent.com/u/14053391?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'Levure',
            'url' => 'https://github.com/Levure',
            'photo' => 'https://avatars0.githubusercontent.com/u/1922257?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'c33s',
            'url' => 'https://github.com/c33s',
            'photo' => 'https://avatars3.githubusercontent.com/u/649209?v=4',
            'contribution_count' => 1,
        ], [
            'name' => 'alfredbez',
            'url' => 'https://github.com/alfredbez',
            'photo' => 'https://avatars0.githubusercontent.com/u/1001186?v=4',
            'contribution_count' => 1,
        ]]
    );
};
