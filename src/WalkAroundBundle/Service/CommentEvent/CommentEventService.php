<?php


namespace WalkAroundBundle\Service\CommentEvent;


use DateTime;
use Exception;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\Event;
use WalkAroundBundle\Entity\EventComment;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\EventCommentRepository;

class CommentEventService implements CommentEventServiceInterface
{

    private $commentRepo;
    private $security;

    public function __construct(
        EventCommentRepository $commentRepo,
        Security $security
    )
    {
        $this->commentRepo = $commentRepo;
        $this->security = $security;
    }

    /**
     * @param int $id
     * @return object|EventComment|null
     */
    public function getCommentById(int $id):?EventComment
    {
        return $this->commentRepo->find($id);
    }

    /**
     * @param Event $event
     * @return EventComment[]|null
     */
    public function getCommentsByEvent(Event $event):array {
        return $this->commentRepo->findBy(['eventId' => $event->getId() ] );
    }

    /**
     * @param EventComment $comment
     * @param Event $event
     * @return bool
     * @throws \Exception
     */
    public function writeComment( $comment,  $event): bool
    {
        if($comment->getContent() == null ) {
            throw new Exception('Error empty message');
        }

        /** @var User $current */
        $current = $this->security->getUser();
        $comment
            ->setEvent( $event )
            ->setAddedOn( new DateTime('now'))
            ->setAddUser( $current );

        if( $this->commentRepo->insert( $comment ) )
            return true;

        return false;
    }

    /**
     * @param EventComment $comment
     * @return bool
     */
    public function deleteComment(EventComment $comment) {

        return $this->commentRepo->delete( $comment );
    }
}