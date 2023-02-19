<?php

declare(strict_types=1);

namespace TomasVotruba\Utils\Rector\Rector\PostRector;

use Rector\PostRector\Rector\AbstractPostRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use TomasVotruba\Utils\Rector\DataCollector\RouteDataCollector;

final class DumpRouteFilePostRector extends AbstractPostRector
{
    public function __construct(
        private readonly RouteDataCollector $routeDataCollector,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        // TODO: Implement getRuleDefinition() method.
    }

    public function beforeTraverse(array $nodes)
    {
        dump($this->routeDataCollector->getCollectedRouteData());
        die;

        // dump routes/web.php if not exists yet

        return parent::beforeTraverse($nodes);
    }

    public function getPriority(): int
    {
        return 520;
    }
}
