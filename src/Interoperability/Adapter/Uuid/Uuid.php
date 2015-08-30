<?php

/*
 * This file is part of Tomasvotruba.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Interperability\Adapter\Uuid;

use Interoperability\Contract\Uuid\UuidInterface;
use Rhumsaa\Uuid\Uuid as RhumsaaUuid;

final class Uuid implements UuidInterface
{
    /**
     * {@inheritdoc}
     */
    public static function uuid4()
    {
        return RhumsaaUuid::uuid4()->toString();
    }
}
