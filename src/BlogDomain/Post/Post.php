<?php

/*
 * This file is part of Tomasvotruba.cz
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace BlogDomain\Post;

final class Post
{
    private $id;

    public function __construct(PostId $id)
    {
        $this->id = $id;
    }

    /**
     * @return PostId
     */
    public function getId()
    {
        return $this->id;
    }
}
