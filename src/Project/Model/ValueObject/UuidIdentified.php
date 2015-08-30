<?php

/*
 * This file is part of Symfonisti.cz
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Project\Model\ValueObject;

use Assert\Assertion;
use Rhumsaa\Uuid\Uuid;

final class UuidIdentified
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
        $value = $value ?: Uuid::uuid4()->toString();
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
