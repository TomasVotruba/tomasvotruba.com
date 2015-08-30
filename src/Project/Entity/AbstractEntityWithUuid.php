<?php

/*
 * This file is part of Symfonisti.cz
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Project\Entity;


use Project\Model\ValueObject\UuidIdentified;

abstract class AbstractEntityWithUuid
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @var UuidIdentified
     */
    private $id;

    public function __construct(UuidIdentified $id)
    {
        $this->id = $id;
    }

    /**
     * @return UuidIdentified
     */
    final public function getId()
    {
        return new UuidIdentified($this->id);
    }
}
