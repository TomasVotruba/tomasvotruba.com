<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

return (new Configuration())
    // always used via laravel
    ->ignoreErrorsOnPackage(
        'symfony/http-foundation',
        [\ShipMonk\ComposerDependencyAnalyser\Config\ErrorType::SHADOW_DEPENDENCY]
    );
