<?php

/*
 * This file is part of Tomasvotruba.cz
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace BlogDomain\Post;

use AppBundle\Adapter\Assertion;
use AppBundle\Adapter\Uuid\Uuid;

final class PostId
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value = NULL)
    {
        $value = $value ?: Uuid::uuid4();
        Assertion::uuid($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
