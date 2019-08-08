<?php

namespace WalkAroundBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Entity\CommentDestination;
use WalkAroundBundle\Form\Comment\CommentCreateType;
use WalkAroundBundle\Service\CommentDestination\CommentDestinationServiceInterface;
use WalkAroundBundle\Service\Destination\DestinationServerInterface;

class CommentController extends Controller
{
    private $destService;
    private $commentDestService;

    const SUCCESS_COMMENT = 'Success comment destination!';

    public function __construct(
        DestinationServerInterface $destService,
        CommentDestinationServiceInterface $commentDestService
    )
    {
        $this->destService = $destService;
        $this->commentDestService = $commentDestService;
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("destination/{id}/cemment/new", name="destination_comment_new", methods={"POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function commentProcess(Request $request, $id) {
        $destinationEntity = $this->destService->findOneById( $id );

        if(!$destinationEntity)
            return $this->goHome();

        $commentEntity = new CommentDestination();
        $form = $this->createForm( CommentCreateType::class, $commentEntity);
        $form->handleRequest( $request );

        if($commentEntity->getIdCommentRe() != null ) {

            $rr = $this->commentDestService->getCommentById( intval( $commentEntity->getIdCommentRe()) );

            if( intval( $rr->getIdCommentRe() ) )
                return $this->redirectToRoute('destination_view', ['id' => $id]);

            $commentEntity->setCommentsRe( $rr );
        }

        if( $this->commentDestService->writeComment($commentEntity, $destinationEntity) ) {
            $this->addFlash('info', self::SUCCESS_COMMENT);
            return $this->redirectToRoute('destination_view', ['id' => $id]);
        }

        return $this->goHome();
    }

    function goHome() {
        return $this->redirectToRoute('homepage');
    }
}
