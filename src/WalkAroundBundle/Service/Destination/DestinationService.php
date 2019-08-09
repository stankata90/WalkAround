<?php


namespace WalkAroundBundle\Service\Destination;

use DateTime;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\DestinationLiked;
use WalkAroundBundle\Entity\Region;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\DestinationRepository;
use WalkAroundBundle\Repository\RegionRepository;
use WalkAroundBundle\Service\CommentDestination\CommentDestinationServiceInterface;
use WalkAroundBundle\Service\DestinationLiked\DestinationLikedServiceInterface;
use WalkAroundBundle\Service\User\UserServiceInterface;

class DestinationService implements DestinationServerInterface
{
    private $userService;
    private $destinationRepository;
    private $regionRepository;
    private $security;
    private $commentDestinationService;
    private $likedDestinationService;
    function __construct(
        UserServiceInterface $userService,
        DestinationRepository $destinationRepository,
        RegionRepository $regionRepository,
        Security $security,
        CommentDestinationServiceInterface $commentDestinationService,
        DestinationLikedServiceInterface $destinationLikedService
    )
    {
        $this->userService = $userService;
        $this->destinationRepository = $destinationRepository;
        $this->regionRepository = $regionRepository;
        $this->security = $security;
        $this->commentDestinationService = $commentDestinationService;
        $this->likedDestinationService = $destinationLikedService;
    }

    public function save(Destination $destination): bool
    {
        /** @var Region $regionEntity */
        $regionEntity = $this->regionRepository->find( $destination->getRegionId() );
        if ( !$regionEntity )
            return false;

        $destination
            ->setCountSeen(0)
            ->setCountVisited(0)
            ->setCountLiked(0)
            ->setAddedOn( new DateTime('now'))
            ->setAddedUser( $this->security->getUser() )
            ->setRegion( $regionEntity );

        return $this->destinationRepository->insert( $destination );
    }

    public function update(Destination $destination): bool
    {
        return $this->destinationRepository->update( $destination);
    }

    public function remove(Destination $destination): bool
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        if( $currentUser->getId() != $destination->getAddedBy() and !$this->userService->isAdmin() ) {
            return false;
        }

        $this->commentDestinationService->removeCommentsByDestination( $destination );
        $this->likedDestinationService->removeLikesByDestination( $destination );
        $image = 'uploads/images/destination/'.$destination->getImage();

        if(file_exists( $image ))
            unlink( $image );

        return  $this->destinationRepository->delete($destination);
    }

    /**
     * @param Destination $destination
     * @return Destination|null|object
     */
    public function findOne(Destination $destination): ?Destination
    {
        return $this->destinationRepository->find( $destination );
    }

    /**
     * @param int $id
     * @return Destination|null|object
     */
    public function findOneById(int $id): ?Destination
    {
        return $this->destinationRepository->findOneBy( [ 'id'=> $id ] );
    }

    public function findAll()
    {
        return $this->destinationRepository->findAll();
    }

    public function addSeenCount( Destination $destination ) {
        $destination->setCountSeen($destination->getCountSeen() + 1 );

        $this->destinationRepository->update( $destination );
    }


    public function viewDependence(Destination $destination)
    {
        $this->addSeenCount( $destination );

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        /** @var DestinationLiked $destinationLikedEntity */

        $destinationLikedEntity = NULL;

        if( $currentUser !== NULL )
            $destinationLikedEntity = $this->likedDestinationService->findLike( $destination, $currentUser );

        return [
            'destinationLikedEntity' => $destinationLikedEntity
        ];
    }
}