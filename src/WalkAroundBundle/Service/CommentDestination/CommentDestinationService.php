<?php


namespace WalkAroundBundle\Service\CommentDestination;


use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\CommentDestination;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\CommentDestinationRepository;
use WalkAroundBundle\Service\Destination\DestinationServerInterface;

class CommentDestinationService implements CommentDestinationServiceInterface
{
    private $commentDestinationRepository;
    private $security;
    public function __construct(
        CommentDestinationRepository $commentDestinationRepository,
        Security $security
    )
    {
        $this->commentDestinationRepository = $commentDestinationRepository;
        $this->security = $security;

    }

    public function writeComment(CommentDestination $comment, Destination $destination): bool
    {
        /** @var User $current */
        $current = $this->security->getUser();
        $comment
            ->setDestination( $destination )
            ->setAddedOn( new \DateTime('now'))
            ->setAddedUser( $current );

        if( $this->commentDestinationRepository->insert( $comment ) )
            return true;

        return false;
    }

    public function removeComment(CommentDestination $comment): bool
    {
        if( $this->commentDestinationRepository->delete( $comment ) )
            return true;

        return false;
    }

    /**
     * @param int $id
     * @return CommentDestination|null|object
     */
    public function getCommentById(int $id): ?CommentDestination
    {
        return $this->commentDestinationRepository->find($id);
    }

    /**
     * @param User $author
     * @return CommentDestination|null|object
     */
    public function getCommentByAuthor(User $author): ?CommentDestination
    {
        return $this->commentDestinationRepository->find( $author );
    }

    /**
     * @param int $authorId
     * @return CommentDestination|null|object
     */
    public function getCommentByAuthorId(int $authorId): ?CommentDestination
    {
        return $this->commentDestinationRepository->findOneBy(['addedBy' => $authorId] );
    }

    /**
     * @param Destination $destination
     * @return CommentDestination[]|null|object
     */
    public function getCommentByDestination(Destination $destination)
    {
        return $this->commentDestinationRepository->findOneByDestination( $destination );
    }

    /**
     * @param int $id
     * @return CommentDestination|null|object
     */
    public function getCommentByDestinationId(int $id): ?CommentDestination
    {
        return $this->commentDestinationRepository->findOneBy(['destination_id' => $id] );
    }

    /**
     * @param int $reId
     * @return CommentDestination|null|object
     */
    public function geCommentByReId( int $reId ): ?CommentDestination
    {
        return $this->commentDestinationRepository->findOneBy(['idCommentRe' => $reId ] );
    }


    public function likeComment(CommentDestination $comment): bool
    {
        /** @var User $current */
        $current = $this->security->getUser();


        // TODO: Implement likeComment() method.
    }

    public function unlikeComment(CommentDestination $coment): bool
    {
        /** @var User $current */
        $current = $this->security->getUser();

        // TODO: Implement unlikeComment() method.
    }


    public function removeCommentsByDestination(Destination $destination): bool
    {
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
        return $this->commentDestinationRepository->findBy(['destinationId'=> $destination->getId()]);
    }
}