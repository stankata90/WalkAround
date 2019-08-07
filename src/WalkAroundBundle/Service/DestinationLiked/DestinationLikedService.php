<?php


namespace WalkAroundBundle\Service\DestinationLiked;


use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\DestinationLiked;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\DestinationLikedRepository;
use WalkAroundBundle\Repository\DestinationRepository;
use WalkAroundBundle\Repository\UserRepository;

class DestinationLikedService implements DestinationLikedServiceInterface
{
    private $destinationRepository;
    private $userRepository;
    private $destinationLikedRepository;

    public function __construct(
        DestinationRepository $destinationRepository,
        UserRepository $userRepository,
        DestinationLikedRepository $destinationLikedRepository
    )
    {
        $this->destinationRepository = $destinationRepository;
        $this->userRepository = $userRepository;
        $this->destinationLikedRepository = $destinationLikedRepository;
    }

    public function like(Destination $destination, User $user)
    {

        if( count( $this->findLike( $destination, $user ) ) ) {
            return false;
        }

        /** @var DestinationLiked $destinationLikedEntity */
        $destinationLikedEntity = new DestinationLiked();
        $destinationLikedEntity
            ->setLikedDestination( $destination )
            ->setLikedUser( $user )
            ->setAddedOn( new \DateTime('now'));

        if( $this->destinationLikedRepository->insertLike( $destinationLikedEntity ) )
            return true;
        else
            return false;
    }

    public function unlike(Destination $destination, User $user)
    {
        /** @var DestinationLiked[] $destinationLikedEntity */
        $destinationLikedEntity = $this->findLike( $destination, $user );

        if( isset($destinationLikedEntity[0]) ) {

            if( $this->destinationLikedRepository->deleteLike( $destinationLikedEntity[0] ) )
                return true;
            else
                return false;
        }

        return false;
    }

    /**
     * @param Destination $destination
     * @param User $user
     * @return object|DestinationLiked[]
     */
    public function findLike(Destination $destination, User $user)
    {
        return $this->destinationLikedRepository->findOne( $destination, $user );
    }

    public function removeLikesByDestination(Destination $destination)
    {
        foreach ( $this->destinationLikedRepository->findBy(['destinationId'=>$destination]) as $like ) {
            $this->destinationLikedRepository->deleteLike( $like );
        }

        return true;
    }
}