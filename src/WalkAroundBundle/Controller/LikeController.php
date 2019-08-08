<?php

namespace WalkAroundBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Service\CommentDestination\CommentDestinationServiceInterface;
use WalkAroundBundle\Service\Destination\DestinationServerInterface;
use WalkAroundBundle\Service\DestinationLiked\DestinationLikedServiceInterface;
use WalkAroundBundle\Service\LikeCommentDestination\LikeCommentDestinationService;

class LikeController extends Controller
{
    private $destService;
    private $likeDestService;
    private $commentDestService;
    private $likeCommentDestService;

    const SUCCESS_LIKE = 'Success liked destination!';

    public function __construct(
        DestinationServerInterface $destService,
        DestinationLikedServiceInterface  $destLikedService,
        CommentDestinationServiceInterface $commentDestService,
        LikeCommentDestinationService $likeCommentDestService
    )
    {
        $this->destService = $destService;
        $this->likeDestService = $destLikedService;
        $this->commentDestService = $commentDestService;
        $this->likeCommentDestService = $likeCommentDestService;
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("destination/{id}/like", name="destination_like", methods={"GET"}, requirements={"id"="\d+"} )
     * @param int $id
     * @return RedirectResponse
     */
    public function likeDestProcess(int $id) {
        $destinationEntity = $this->destService->findOneById( $id ) ;

        if(!$destinationEntity)
            return $this->goHome();


        if( $this->likeDestService->like( $destinationEntity, $this->getUser() ) ) {
            $destinationEntity->setCountLiked( $destinationEntity->getCountLiked()+1 );
            $this->destService->update( $destinationEntity );
            $this->addFlash('info', self::SUCCESS_LIKE);
        }

        return $this->redirectToRoute( 'destination_view', ['id' => $id] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("destination/{id}/unlike", name="destination_unlike", methods={"GET"}, requirements={"id"="\d+"} )
     * @param int $id
     * @return RedirectResponse
     */
    public function unlikeProcess(int $id) {

        $destinationEntity = $this->destService->findOneById( $id ) ;

        if(!$destinationEntity)
            return $this->goHome();

        if( $this->likeDestService->unlike( $destinationEntity, $this->getUser() ) ) {
            $destinationEntity->setCountLiked( $destinationEntity->getCountLiked()-1 );
            $this->destService->update( $destinationEntity );
        }
        return $this->redirectToRoute( 'destination_view', ['id' => $id] );
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("commentDestination/{id}/liked", name="liked_comment_destination", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @return RedirectResponse
     */
    public function likeComment( $id ) {
        $commentEntity = $this->commentDestService->getCommentById( $id );

        if(!$commentEntity)
            return $this->goHome();


        $this->likeCommentDestService->addLike( $commentEntity );

        return $this->redirectToRoute('destination_view', ['id' => $commentEntity->getDestinationId()] );
    }

    function goHome() {
        return $this->redirectToRoute('homepage');
    }
}
