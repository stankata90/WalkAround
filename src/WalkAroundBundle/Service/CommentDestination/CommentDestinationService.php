<?php


namespace WalkAroundBundle\Service\CommentDestination;


use DateTime;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\CommentDestination;
use WalkAroundBundle\Entity\CommentDestinationLiked;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\CommentDestinationRepository;
use WalkAroundBundle\Service\LikeCommentDestination\LikeCommentDestinationServiceInterface;

class CommentDestinationService implements CommentDestinationServiceInterface
{
    private $commentRepo;
    private $security;
    private $likeService;

    public function __construct(
        CommentDestinationRepository $commentRepo,
        Security $security,
        LikeCommentDestinationServiceInterface $likeService
    )
    {
        $this->commentRepo = $commentRepo;
        $this->security = $security;
        $this->likeService = $likeService;
    }

    public function writeComment(CommentDestination $comment, Destination $destination): bool
    {
        /** @var User $current */
        $current = $this->security->getUser();
        $comment
            ->setDestination( $destination )
            ->setAddedOn( new DateTime('now'))
            ->setAddedUser( $current );

        if( $this->commentRepo->insert( $comment ) )
            return true;

        return false;
    }

    public function removeComment(CommentDestination $comment): bool
    {
        if( $this->commentRepo->delete( $comment ) )
            return true;

        return false;
    }

    /**
     * @param int $id
     * @return CommentDestination|null|object
     */
    public function getCommentById(int $id): ?CommentDestination
    {
        return $this->commentRepo->find($id);
    }

    /**
     * @param User $author
     * @return CommentDestination|null|object
     */
    public function getCommentByAuthor(User $author): ?CommentDestination
    {
        return $this->commentRepo->find( $author );
    }

    /**
     * @param int $authorId
     * @return CommentDestination|null|object
     */
    public function getCommentByAuthorId(int $authorId): ?CommentDestination
    {
        return $this->commentRepo->findOneBy(['addedBy' => $authorId] );
    }

    /**
     * @param Destination $destination
     * @return CommentDestination[]|null|object
     */
    public function getCommentByDestination(Destination $destination)
    {
        return $this->commentRepo->findOneByDestination( $destination );
    }

    /**
     * @param int $id
     * @return CommentDestination|null|object
     */
    public function getCommentByDestinationId(int $id): ?CommentDestination
    {
        return $this->commentRepo->findOneBy(['destination_id' => $id] );
    }

    /**
     * @param int $reId
     * @return CommentDestination|null|object
     */
    public function geCommentByReId( int $reId ): ?CommentDestination
    {
        return $this->commentRepo->findOneBy(['idCommentRe' => $reId ] );
    }


    public function likeComment(CommentDestination $comment): bool
    {

        return false;
    }

    public function unlikeComment(CommentDestination $coment): bool
    {

        return false;
    }


    public function removeCommentsByDestination(Destination $destination): bool
    {
        foreach ( $this->getCommentsByDestination( $destination ) as $comment ) {

            foreach ( $comment->getLikes() as $like ) {
                /** @var CommentDestinationLiked $like */
                $this->likeService->remove( $like );
            }
            if( $comment->getIdCommentRe() ) {
                $this->removeComment( $comment );
            }
        }

        foreach ( $this->getCommentsByDestination( $destination ) as $comment ) {
            $this->removeComment( $comment );
        }

        return true;
    }

    /**
     * @param Destination $destination
     * @return CommentDestination[]|null
     */
    public function getCommentsByDestination(Destination $destination)
    {
        return $this->commentRepo->findBy(['destinationId'=> $destination->getId()]);
    }

}