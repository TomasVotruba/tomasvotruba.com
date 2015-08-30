<?php

/*
 * This file is part of Tomasvotruba.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Interoperability\Contract\Uuid;

interface UuidInterface
{
    /**
     * @return string
     */
    static function uuid4();
}
