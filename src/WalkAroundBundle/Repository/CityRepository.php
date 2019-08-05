<?php

namespace WalkAroundBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use WalkAroundBundle\Entity\User;

/**
 * CityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CityRepository extends EntityRepository
{
    public function __construct( EntityManagerInterface $em, Mapping\ClassMetadata $class = null )
    {
        /** @var EntityManager $em */
        parent::__construct($em, $class == null ? new Mapping\ClassMetadata( User::class ) : $class );
    }
}
