<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('talks_feedback', [
        [
            'name' => 'Iurii Golikov',
            'photo' => 'https://secure.gravatar.com/avatar/019533bbd162b35aaf403145491843c1?d=mm&s=200',
            'feedback' => 'Great tool, great presentation and nice font size on the demo!',
            'rating' => 5,
        ], [
            'name' => 'Sascha-Oliver Prolic',
            'photo' => 'https://secure.gravatar.com/avatar/4256101aea1d08865fdc36ad653f5e11?d=mm&s=200',
            'feedback' => 'I wish I knew about rector a few years ago. Great talk!',
            'rating' => 4,
        ], [
            'name' => 'Michael Bush',
            'photo' => 'https://secure.gravatar.com/avatar/630071ac3f9939b36ff8afa8bf7470c4?d=mm&s=100',
            'feedback' => 'Confident speaker, looked really happy to be giving the talk!',
            'rating' => 5,
        ],
    ]);
};
