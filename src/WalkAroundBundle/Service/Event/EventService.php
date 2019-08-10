<?php


namespace WalkAroundBundle\Service\Event;


use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Controller\EventController;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\Event;
use WalkAroundBundle\Entity\EventComment;
use WalkAroundBundle\Entity\EventUser;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Form\Event\EventCreateType;
use WalkAroundBundle\Repository\EventRepository;
use WalkAroundBundle\Repository\EventUserRepository;
use WalkAroundBundle\Service\CommentEvent\CommentEventServiceInterface;
use WalkAroundBundle\Service\Friend\FriendServiceInterface;
use WalkAroundBundle\Service\User\UserServiceInterface;

class EventService implements EventServiceInterface
{
    private $security;
    private $eventRepo;
    private $eventUserRepo;
    private $userService;
    private $friendService;
    private $commentService;

    public function __construct(
        Security $security,
        EventRepository $eventRepository,
        UserServiceInterface $userService,
        FriendServiceInterface $friendService,
        EventUserRepository $eventUserRepo,
        CommentEventServiceInterface $commentService
    )
    {
        $this->security = $security;
        $this->eventRepo = $eventRepository;
        $this->userService = $userService;
        $this->friendService = $friendService;
        $this->eventUserRepo = $eventUserRepo;
        $this->commentService = $commentService;
    }

    /**
     * @param EventController $controller
     * @param Request $request
     * @param $form
     * @param Destination $destEntity
     * @return Event
     * @throws Exception
     */
    public function createProcess( EventController $controller, $request, &$form, $destEntity )
    {
        /** @var User $current */
        $current =  $this->security->getUser();

        /** @var Event $eventEntity */
        $eventEntity = new Event();


        $form = $controller->createForm( EventCreateType::class, $eventEntity );
        $form->handleRequest( $request );

        $eventOn = $eventEntity->getEventOn();

        if( !$this->verifyDate( $eventOn ))
            throw new Exception('Invalid Date');

        try{
            $date = new DateTime( $eventOn );
        } catch ( Exception $e) {
            throw new Exception('Invalid Date');
        }

            $eventEntity
                ->setDestination($destEntity)
                ->setAddUser($current)
                ->setAddedOn(new DateTime('now'))
                ->setEventOn( $date )
                ->setStatus(0)
            ;

        $this->eventRepo->insert( $eventEntity );

        $arrUsersId =  ( key_exists('eventUsers', $request->request->get('event') ) ) ? $request->request->get('event')['eventUsers'] : [] ;
         if ( !count( $arrUsersId ) )
             throw new Exception('Select friends');

        $arrUsersId[] = $current->getId();

        foreach ( $arrUsersId as $id ) {
            /** @var User $userEntity */
           $userEntity = $this->userService->findOneById( intval( $id ) );
           if( !$userEntity )
               continue;

           if( !$this->friendService->findFriend( $userEntity ) AND $userEntity->getId() != $current->getId() )
               continue;

           /** @var EventUser $eventUser */
           $eventUser = new EventUser();
           $eventUser
                ->setEvent( $eventEntity )
                ->setUser( $userEntity );

           if( $userEntity->getId() == $current->getId() )
               $eventUser->setAccepted( new DateTime('now' ) );

           $this->eventUserRepo->insert( $eventUser );

        }

        return $eventEntity;
    }

    public function acceptProcess(EventUser $eventUser)
    {
       $eventUser
           ->setAccepted(new DateTime());

       $this->eventUserRepo->update( $eventUser );

       return true;
    }

    public function leaveProcess(EventUser $eventUser)
    {
        $this->eventUserRepo->delete( $eventUser );

        return true;
    }

    public function endProcess(Event $event)
    {
        foreach ( $this->findById( $event->getId() ) as $userForLeave ) {
            /** @var EventUser $userForLeave */
            $this->eventUserRepo->delete( $userForLeave );
        }

        $event
            ->setEndOn( new DateTime() )
            ->setStatus( 1 )
        ;

        $this->eventRepo->update( $event );

        return true;
    }

    public function dropProcess( Event $event) {

        $allEventComments = $this->commentService->getCommentsByEvent( $event );
        foreach ( $allEventComments as $comment ) {
            /** @var EventComment $comment */
            if( $comment->getIdCommentRe() != null)
                $this->commentService->deleteComment( $comment );
        }

        $allEventComments = $this->commentService->getCommentsByEvent( $event );
        foreach ( $allEventComments as $comment ) {
            /** @var EventComment $comment */
                $this->commentService->deleteComment( $comment );
        }


        $allInvitedUsers = $this->findInvitedUsers( $event );

        foreach ( $allInvitedUsers as $allInvitedUser) {

           $this->eventUserRepo->delete( $allInvitedUser) ;
        }

        $this->eventRepo->delete( $event );

        return true;
    }




    public function findById(int $id)
    {
        return $this->eventUserRepo->findBy(['eventId' => $id, 'accepted' => null ]);
    }

    public function verifyDate($date)
    {
        return (DateTime::createFromFormat('Y/m/d H:i', $date) !== false);
    }

    /**
     * @param int $id
     * @return Event|null|object
     */
    public function findOneById(int $id): ?object
    {
        return $this->eventRepo->find( $id );
    }

    public function findInvitedById($id)
    {
        /** @var User $current */
        $current = $this->security->getUser();

        return $this->eventUserRepo->findOneBy(['eventId' => $id, 'userId' => $current->getId()]);
    }

    /**
     * @param Event $event
     * @return EventUser[]|null
     */
    public function findInvitedUsers( Event $event ) :?array {
        return $this->eventUserRepo->findBy(['eventId' => $event->getId()]);
    }


    public function isCompleted(Event $event)
    {
        return ( $this->eventRepo->findOneBy(['id' => $event->getId(), 'status' => 1 ] ) != null );
    }
}