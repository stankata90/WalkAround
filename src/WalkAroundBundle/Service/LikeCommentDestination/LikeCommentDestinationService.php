<?php


namespace WalkAroundBundle\Service\LikeCommentDestination;



use DateTime;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\CommentDestination;
use WalkAroundBundle\Entity\CommentDestinationLiked;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\CommentDestinationLikedRepository;
use WalkAroundBundle\Service\CommentDestination\CommentDestinationServiceInterface;

class LikeCommentDestinationService implements LikeCommentDestinationServiceInterface
{
    private $likeCommentRepository;
    private $security;
    /**
     * @var CommentDestinationServiceInterface
     */

    public function __construct(
        CommentDestinationLikedRepository $commentDestinationLikedRepository,
        Security $security
    )
    {
        $this->likeCommentRepository = $commentDestinationLikedRepository;
        $this->security = $security;
    }

    public function add( CommentDestinationLiked $like )
    {
        return $this->likeCommentRepository->insert( $like );
    }


    public function remove( CommentDestinationLiked $like)
    {
        return $this->likeCommentRepository->delete( $like );
    }

    public function finOne(int $likeId)
    {
        return false;
    }

    public function addLike(CommentDestination $commentDestination )
    {


//        $commentEntity = $this->commentService->getCommentById( $commentDestination );





        /** @var User $userEntity */
        $userEntity = $this->security->getUser();

        $likeEntity = new CommentDestinationLiked();
        $likeEntity
            ->setCommentDestination( $commentDestination )
            ->setAddUser( $userEntity )
            ->setAddedOn( new DateTime('now'));

        return $this->add( $likeEntity);
    }
}