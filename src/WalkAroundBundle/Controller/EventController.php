<?php

namespace WalkAroundBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\Event;
use WalkAroundBundle\Entity\EventUser;
use WalkAroundBundle\Entity\Friend;
use WalkAroundBundle\Form\Comment\CommentEventCreateType;
use WalkAroundBundle\Form\Event\EventCreateType;
use WalkAroundBundle\Repository\DestinationRepository;
use WalkAroundBundle\Service\Event\EventServiceInterface;
use WalkAroundBundle\Service\Friend\FriendServiceInterface;

class EventController extends Controller
{
    const SUCCESS_ADD = "You have successfully create new event";
    const SUCCESS_AC = "You have successfully accept event";
    const SUCCESS_LEAVE = "You have successfully left event";
    const SUCCESS_END = "You have successfully end of the event";
    const SUCCESS_DROP = "You have successfully drop event";


    private $destRepo;
    private $friendService;
    private $eventService;

    public function __construct(
        DestinationRepository $destRepo,
        FriendServiceInterface $friendService,
        EventServiceInterface $eventService
    )
    {
         $this->destRepo = $destRepo;
         $this->friendService = $friendService;
         $this->eventService = $eventService;
    }

    public function createForm($type, $data = null, array $options = [])
    {
        return parent::createForm( $type, $data, $options);
    }

    public function getParameter($name)
    {
        return parent::getParameter($name);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("event/{id}/view/{comment}", defaults={"comment"="0"}, name="event_view", requirements={"id"="\d+","comment"="\d+"}, methods={"GET"})
     * @param $id
     * @param int|null $comment
     * @return Response
     */
    public function viewAction( $id, int $comment = null )
    {
        /** @var Event $eventEntity */
        $eventEntity = $this->eventService->findOneById( intval( $id) );

        if( !$eventEntity )
            return $this->goHome();

        if( !$this->eventService->findInvitedById( $id ) )
            return $this->goHome();

        return $this->render('event/view.html.twig', [
            'event' => $eventEntity,
            'formComment' => $this->createForm( CommentEventCreateType::class )->createView(),
            're' => $comment
        ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("event/create/{id}", name="event_create", requirements={"id"="\d+"}, methods={"GET"})
     * @param $id
     * @return Response
     */
    public function createAction( $id )
    {
        $destEntity = $this->destRepo->find( intval( $id ) );

        if( !$destEntity )
            return $this->goHome();

        /** @var Friend $friends */
        $friends = $this->friendService->findAll();

        return $this->render('event/crete.html.twig', [
            'form' => $this->createForm(EventCreateType::class )->createView(),
            'dest' => $destEntity,
            'friends' => $friends
        ] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("event/create/{id}", name="event_create_process", requirements={"id"="\d+"}, methods={"POST"})
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function createProcess( $id, Request  $request )
    {
        /** @var Destination $destEntity */
        $destEntity = $this->destRepo->find( intval( $id ) );

        if( !$destEntity )
            return $this->goHome();
        try{
            /** @var Event $eventEntity */
            $eventEntity = $this->eventService->createProcess( $this, $request, $form, $destEntity);
            $this->addFlash('info', self::SUCCESS_ADD );

        } catch ( Exception $e ) {

            $this->addFlash('error',  $e->getMessage() );
            return $this->redirectToRoute('event_create', ['id'=>$id]);
        }

        return $this->redirectToRoute('event_view', [ 'id'=>$eventEntity->getId() ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("event/accept/{id}", name="event_accept_process", requirements={"id"="\d+"}, methods={"GET"})
     * @param $id
     * @return Response
     */
    public function acceptProcess( $id )
    {
        /** @var Event $eventEntity */
        $eventEntity = $this->eventService->findOneById( intval( $id) );

        if( !$eventEntity )
            return $this->goHome();

        /** @var EventUser $eventUserEntity */
        $eventUserEntity = $this->eventService->findInvitedById( intval( $id) );

        if( !$eventUserEntity or $eventUserEntity->getAccepted() != null )
           return $this->goHome();

        $this->eventService->acceptProcess( $eventUserEntity );

        $this->addFlash('info', self::SUCCESS_AC );

        return $this->redirectToRoute( 'event_view', [ 'id' => $id] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("event/end/{id}", name="event_end_process", requirements={"id"="\d+"}, methods={"GET"})
     * @param $id
     * @return Response
     */
    public function endProcess( $id ) {
        /** @var Event $eventEntity */
        $eventEntity = $this->eventService->findOneById( intval( $id) );

        if( !$eventEntity )
            return $this->goHome();

        if( $eventEntity->getUserId() != $this->getUser()->getId()  )
            return $this->goHome();

        if( $this->eventService->isCompleted($eventEntity) )
            return $this->goHome();

        $this->eventService->endProcess($eventEntity);

        $this->addFlash('info', self::SUCCESS_END);

        return $this->goHome();
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("event/leave/{id}", name="event_leave_process", requirements={"id"="\d+"}, methods={"GET"})
     * @param $id
     * @return Response
     */
    public function leaveProcess( $id )
    {

        /** @var Event $eventEntity */
        $eventEntity = $this->eventService->findOneById( intval($id) );

        if( !$eventEntity and !$eventEntity->getEndOn() )
            return $this->goHome();

        /** @var EventUser $eventUserEntity */
        $eventUserEntity = $this->eventService->findInvitedById( intval( $id) );

        if( !$eventUserEntity )
            $this->goHome();

        $this->addFlash('info', self::SUCCESS_LEAVE);

        $this->eventService->leaveProcess( $eventUserEntity );

        return $this->redirectToRoute( 'event_view', [ 'id' => $id] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("event/drop/{id}", name="event_drop_process", requirements={"id"="\d+"}, methods={"GET"})
     * @param $id
     * @return Response
     */
    public function dropProcess( $id ) {
        /** @var Event $eventEntity */
        $eventEntity = $this->eventService->findOneById( intval($id) );

        if( !$eventEntity and !$eventEntity->getEndOn() )
            return $this->goHome();

        if( $this->eventService->isCompleted( $eventEntity ) )
            return $this->goHome();

        if( $eventEntity->getUserId() != $this->getUser()->getId() )
            return $this->goHome();

        $this->eventService->dropProcess( $eventEntity );

        $this->addFlash('info', self::SUCCESS_DROP);

        return $this->goHome();
    }

    public function goHome() {
        return $this->redirectToRoute('homepage');
    }
}
