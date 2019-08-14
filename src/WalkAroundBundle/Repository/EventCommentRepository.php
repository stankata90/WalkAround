<?php

namespace WalkAroundBundle\Repository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use WalkAroundBundle\Entity\Event;
use WalkAroundBundle\Entity\EventComment;

/**
 * EventCommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventCommentRepository extends WalkRepository
{
    public function __construct( EntityManagerInterface $em, Mapping\ClassMetadata $class = null )
    {
        /** @var EntityManager $em */
        parent::__construct($em, $class == null ? new Mapping\ClassMetadata( EventComment::class ) : $class );
    }

    /**
     * @param Event $event
     * @return EventComment[]
     */
    public function findOneByevent( Event $event) {
        $query = $this->createQueryBuilder('c')
            ->where('c.eventId = :id')
            ->setParameter('id', $event->getId())
            ->getQuery();

        $products = $query->getResult();
        return $products;
    }
}
