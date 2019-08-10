<?php

namespace WalkAroundBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Entity\CommentDestination;
use WalkAroundBundle\Entity\Event;
use WalkAroundBundle\Entity\EventComment;
use WalkAroundBundle\Form\Comment\CommentDestCreateType;
use WalkAroundBundle\Form\Comment\CommentEventCreateType;
use WalkAroundBundle\Service\CommentDestination\CommentDestinationServiceInterface;
use WalkAroundBundle\Service\CommentEvent\CommentEventServiceInterface;
use WalkAroundBundle\Service\Destination\DestinationServerInterface;
use WalkAroundBundle\Service\Event\EventServiceInterface;

class CommentController extends Controller
{

    private $destService;
    private $commentDestService;

    private $eventService;
    private $commentEventService;

    const SUCCESS_COMMENT = 'Success comments destination!';

    public function __construct(
        DestinationServerInterface $destService,
        CommentDestinationServiceInterface $commentDestService,
        EventServiceInterface $eventService,
        CommentEventServiceInterface $commentEventService
    )
    {
        $this->destService = $destService;
        $this->commentDestService = $commentDestService;
        $this->eventService = $eventService;
        $this->commentEventService = $commentEventService;
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("destination/{id}/comment/new", name="destination_comment_new", methods={"POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function commentDestProcess(Request $request, $id ) {
        try{
        $destinationEntity = $this->destService->findOneById( $id );

        if(!$destinationEntity)
            return $this->goHome();

            $commentEntity = new CommentDestination();
            $form = $this->createForm( CommentDestCreateType::class, $commentEntity);
            $form->handleRequest( $request );

            if($commentEntity->getIdCommentRe() != null ) {

                $rr = $this->commentDestService->getCommentById( intval( $commentEntity->getIdCommentRe()) );

                if( intval( $rr->getIdCommentRe() ) )
                    return $this->redirectToRoute('destination_view', ['id' => $id]);

                $commentEntity->setComment( $rr );
            }

            $this->commentDestService->writeComment($commentEntity, $destinationEntity) ;

            $this->addFlash('info', self::SUCCESS_COMMENT);

        }catch ( Exception $e ){
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('destination_view', ['id' => $id]);

    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("event/{id}/comments/new", name="event_comment_new", methods={"POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function commentEventProcess(Request $request, $id ) {
        try {

        /** @var Event $eventEntity */
        $eventEntity = $this->eventService->findOneById( $id );

        if(!$eventEntity)
            return $this->goHome();

            /** @var EventComment $commentEntity */
            $commentEntity = new EventComment();
            $form = $this->createForm(CommentEventCreateType::class, $commentEntity);
            $form->handleRequest($request);

            if ($commentEntity->getIdCommentRe() != null) {

                $rr = $this->commentEventService->getCommentById(intval($commentEntity->getIdCommentRe()));

                if (intval($rr->getIdCommentRe()))
                    return $this->redirectToRoute('event_view', ['id' => $id]);

                $commentEntity->setComment($rr);
            }

            $this->commentEventService->writeComment($commentEntity, $eventEntity);

            $this->addFlash('info', self::SUCCESS_COMMENT);

        } catch ( Exception $e ) {
            $this->addFlash( 'error', $e->getMessage() );
        }

        return $this->redirectToRoute('event_view', ['id' => $id]);
    }

    /**
     * @return RedirectResponse
     */
    function goHome() {
        return $this->redirectToRoute('homepage');
    }
}
