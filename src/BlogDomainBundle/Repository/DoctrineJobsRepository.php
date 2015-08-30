<?php

/*
 * This file is part of Symfonisti.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace AppBundle\Repository;

use AppBundle\Contract\Repository\JobsRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineJobsRepository implements JobsRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
