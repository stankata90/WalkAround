<?php

namespace SoftUniBlogBundle\Repository;

use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;
use SoftUniBlogBundle\Entity\User;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct($em, Mapping\ClassMetadata $class = null )
    {
        parent::__construct($em, $class == null ? new Mapping\ClassMetadata( User::class ) : $class );
    }

    public function insert( User $user ) {
        try {
            $this->_em->persist( $user );
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
}