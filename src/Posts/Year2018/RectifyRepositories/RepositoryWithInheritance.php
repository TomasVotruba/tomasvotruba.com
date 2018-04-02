<?php declare(strict_types=1);

namespace TomasVotruba\Website\Posts\Year2018\RectifyRepositories;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;

final class RepositoryWithInheritance extends EntityRepository
{
    public function getByName()
    {

    }

}
