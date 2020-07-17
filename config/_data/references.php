<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('helped_companies', [
        'lekarna.png' => 'https://www.lekarna.cz/',
        'mall.png' => 'https://www.mall.cz/',
        'shopsys.png' => 'https://www.shopsys.com/',
        'elasticr.png' => 'https://www.elasticr.cz/',
    ]);

    $parameters->set('references', [
        [
            'text' => 'Tomas was able to prepare us lecture fulfilling our specific needs even beyond the area of his portfolio. Thoughts and techniques were clearly provided and easy to comprehend for the whole team.',
            'author' => 'Roman Veselý, Internal Systems Developer',
            'company' => 'Ness KDC',
            'company_url' => 'https://www.nesskdc.sk/en/',
        ],
        [
            'text' => 'Tomas doesn\'t speak about theory you can read on the Internet. He passes you his practical experiences. I recommend him to everyone who\'s looking for a specialist.',
            'author' => 'Radek Strnad, PHP Teamleader',
            'company' => 'Erudio.cz',
            'company_url' => 'http://erudio.cz',
        ],
    ]);
};
