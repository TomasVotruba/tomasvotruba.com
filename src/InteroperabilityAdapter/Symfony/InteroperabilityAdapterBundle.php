<?php

/*
 * This file is part of TomasVotruba.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace InteroperabilityAdapter\Symfony;

use InteroperabilityAdapter\Symfony\DependencyInjection\InteroperabilityAdapterExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class InteroperabilityAdapterBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new InteroperabilityAdapterExtension;
    }
}
