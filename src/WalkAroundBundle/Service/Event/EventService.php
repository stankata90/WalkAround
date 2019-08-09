<?php


namespace WalkAroundBundle\Service\Event;


use DateTime;
use Exception;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Controller\EventController;
use WalkAroundBundle\Entity\Event;
use WalkAroundBundle\Entity\EventUser;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Form\Event\EventCreateType;
use WalkAroundBundle\Repository\EventRepository;
use WalkAroundBundle\Repository\EventUserRepository;
use WalkAroundBundle\Service\Friend\FriendServiceInterface;
use WalkAroundBundle\Service\User\UserServiceInterface;

class EventService implements EventServiceInterface
{
    private $security;
    private $eventRepo;
    private $eventUserRepo;
    private $userService;
    private $friendService;

    public function __construct(
        Security $security,
        EventRepository $eventRepository,
        UserServiceInterface $userService,
        FriendServiceInterface $friendService,
        EventUserRepository $eventUserRepo
    )
    {
        $this->security = $security;
        $this->eventRepo = $eventRepository;
        $this->userService = $userService;
        $this->friendService = $friendService;
        $this->eventUserRepo = $eventUserRepo;
    }

    /**
     * @param EventController $controller
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $form
     * @param \WalkAroundBundle\Entity\Destination $destEntity
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

        $eventOn = str_replace("T", ' ', $eventEntity->getEventOn()  );

        if( !$this->verifyDate( $eventOn ))
            throw new Exception('Invalid Date');


            $eventEntity
                ->setDestination($destEntity)
                ->setAddUser($current)
                ->setAddedOn(new DateTime('now'))
                ->setEventOn( new DateTime( $eventOn ) )
                ->setStatus(0)
            ;


        $this->eventRepo->insert( $eventEntity );
        $arrUsersId = $request->request->get('event')['eventUsers'];
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

    public function findById(int $id)
    {
        return $this->eventUserRepo->findBy(['eventId' => $id, 'accepted' => null ]);
    }

    public function verifyDate($date)
    {
        return (DateTime::createFromFormat('Y-m-d H:i', $date) !== false);
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


    public function isCompleted(Event $event)
    {
        return ( $this->eventRepo->findOneBy(['id' => $event->getId(), 'status' => 1 ] ) != null );
    }
}