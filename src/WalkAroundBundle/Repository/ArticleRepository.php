<?php

namespace SoftUniBlogBundle\Repository;

use Doctrine\ORM\Mapping;
use SoftUniBlogBundle\Entity\Article;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct($em, Mapping\ClassMetadata $class = null )
    {
        parent::__construct($em, $class == null ? new Mapping\ClassMetadata( Article::class ) : $class );
    }
}
