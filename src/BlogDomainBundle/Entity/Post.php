<?php

/*
 * This file is part of TomasVotruba.cz
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace BlogDomainBundle\Entity;

use BlogDomain\Post\PostId;
use Doctrine\ORM\Mapping as ORM;
use Interoperability\Adapter\Assert\Assertion;

/**
 * @ORM\Entity
 * @ORM\Table
 */
final class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @var PostId
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=TRUE)
     * @var string
     */
    private $website;

    /**
     * @param JobId $id
     * @param string $name
     * @param string $website
     */
    public function __construct(PostId $id, $name, $website)
    {
        $this->id = $id;
        $this->setName($name);
        $this->setWebsite($website);
    }

    /**
     * @return PostId
     */
    public function getId()
    {
        return new PostId($this->id);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $name
     */
    private function setName($name)
    {
        Assertion::string($name);
        $this->name = $name;
    }

    /**
     * @param string $website
     */
    private function setWebsite($website)
    {
        Assertion::string($website);
        $this->website = $website;
    }
}
