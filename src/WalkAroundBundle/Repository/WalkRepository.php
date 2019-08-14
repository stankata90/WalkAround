<?php


namespace WalkAroundBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;

class WalkRepository extends EntityRepository
{
    private $className;
    private $classNameSpace;
    public function __construct( EntityManagerInterface $em, Mapping\ClassMetadata $class )
    {
        $classPath = explode( DIRECTORY_SEPARATOR, __CLASS__);
        $this->className = str_replace('Repository', '',  $classPath[ count( $classPath )-1 ] ).'::class';
        $this->classNameSpace = 'WalkAroundBundle\Entity\\'. $this->className;

        /** @var EntityManager $em */
        parent::__construct($em, $class );
    }

    /**
     * @param  $entity
     * @return bool
     */
    public function insert( $entity ) :bool {
        try{
            $this->_em->persist( $entity );
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    /**
     * @param $entity
     * @return bool
     */
    public function update( $entity) {
        try{
            $this->_em->persist( $entity );
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    /**
     * @param $entity
     * @return bool
     */
    public function delete( $entity)
    {
        try{
            $this->_em->remove( $entity );
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
}