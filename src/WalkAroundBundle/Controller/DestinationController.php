<?php

namespace WalkAroundBundle\Controller;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Entity\CommentDestination;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Form\Comment\CommentCreateType;
use WalkAroundBundle\Form\Destination\DestinationCreateType;
use WalkAroundBundle\Form\Destination\DestinationEditType;
use WalkAroundBundle\Service\CommentDestination\CommentDestinationServiceInterface;
use WalkAroundBundle\Service\Destination\DestinationServerInterface;
use WalkAroundBundle\Service\DestinationLiked\DestinationLikedServiceInterface;
use WalkAroundBundle\Service\LikeCommentDestination\LikeCommentDestinationServiceInterface;
use WalkAroundBundle\Service\Region\RegionServiceInterface;

class DestinationController extends Controller
{
    const SUCCESS_ADD = 'Success added destination!';
    const SUCCESS_UPDATE = 'Success updated destination!';
    const SUCCESS_DELETE = 'Success deleted destination!';
    const SUCCESS_COMMENT = 'Success comment destination!';
    const SUCCESS_LIKE = 'Success liked destination!';

    private $destinationService;
    private $regionService;
    private $destinationLikedService;
    private $commentService;
    private $likeCommentService;

    public function __construct(
        DestinationServerInterface $destinationService,
        RegionServiceInterface $regionService,
        DestinationLikedServiceInterface $destinationLikedService,
        CommentDestinationServiceInterface $commentService,
        LikeCommentDestinationServiceInterface $likeCommentService
    )
    {
        $this->destinationService = $destinationService;
        $this->regionService = $regionService;
        $this->destinationLikedService = $destinationLikedService;
        $this->commentService = $commentService;
        $this->likeCommentService = $likeCommentService;
    }

    /**
     * @Route("/destinations", name="destination_all", methods={"Get"})
     * @return Response
     */
    public function indexAction()
    {

        return $this->render('destination/all.html.twig', array('destinations' => $this->destinationService->findAll() ));
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("destination/create", name="destination_create", methods={"GET"})
     */
    public function createAction() {
        return $this->render(
            'destination/create.html.twig',
            [
                'form' => $this->createForm( DestinationCreateType::class )->createView(),
                'regions' => $this->regionService->getAll()
            ]
        );
    }



    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("destination/create", name="destination_create_process", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function createProcess( Request $request ) {

        try{
            $destinationEntity = new Destination();
            $form = $this->createForm( DestinationCreateType::class, $destinationEntity);
            $form->handleRequest( $request );

            /** @var UploadedFile $image */
            $image = $form['image']->getData();

            if( !$image->getError() ) {
                $fileName = md5( uniqid() ) . ".". $image->guessExtension();
                $image->move(
                    $this->getParameter( 'destination_directory'),
                    $fileName
                );
                $destinationEntity->setImage( $fileName );
            }
            $this->destinationService->save( $destinationEntity );
            $this->addFlash('info', self::SUCCESS_ADD);
            return  $this->redirectToRoute( 'destination_view', ['id'=>$destinationEntity->getId()]);
        } catch  ( Exception $e ) {
            $this->addFlash('error', $e->getMessage() );
            return $this->render(
                'destination/create.html.twig',
                [
                    'form' => $form->createView(),
                    'regions' => $this->regionService->getAll()
                ]
            );
        }
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/destination/{id}/edit", name="destination_edit", methods={"GET"}, requirements={"id"="\d+"})
     *
     */
    public function editAction( int $id ) {
        /** @var Destination $destinationEntity */
        $destinationEntity = $this->getDoctrine()->getRepository( Destination::class)->find( $id);
        if( $this->getUser()->getId() != $destinationEntity->getAddedBy() ) {
            return $this->redirectToRoute('homepage' );
        }

        return $this->render(
            'destination/edit.html.twig',
            [
                'form' => $this->createForm( DestinationEditType::class )->createView( ),
                'destination' => $destinationEntity,
                'regions' =>$this->regionService->getAll()
            ]
        );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/destination/{id}/edit", name="destination_edit_process", methods={"POST"}, requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function editProcess( Request $request, int $id) {

        /** @var Destination $destinationEntity*/
        $destinationEntity = $this->getDoctrine()->getRepository(Destination::class)->find( $id );

        if( $this->getUser()->getId() != $destinationEntity->getAddedBy() ) {
            return $this->redirectToRoute('homepage' );
        }

        $oldImage = $destinationEntity->getImage();
        $form = $this->createForm( DestinationEditType::class, $destinationEntity);
        $form->handleRequest(  $request );

        try{
            /** @var UploadedFile $image */
            $image = $form['image']->getData();


            if(  $image !== NULL AND !$image->getError() ) {

               $fileName = md5( uniqid() ) . ".". $image->guessExtension();
               $image->move(
                   $this->getParameter( 'destination_directory'),
                   $fileName
               );

                $file =$this->getParameter( 'destination_directory') ."/". $oldImage;
                if( file_exists( $file ) )
                    unlink( $file );

               $destinationEntity->setImage( $fileName );
           } else {
                $destinationEntity->setImage(  $oldImage );
            }

            $this->destinationService->update( $destinationEntity );
            $this->addFlash('info', self::SUCCESS_UPDATE);
            return  $this->redirectToRoute( 'destination_view',  ['id' => $id] );

        } catch ( Exception $e ) {

            return  $this->redirectToRoute( 'destination_edit',  ['id' => $id]  );
        }

    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route( "destination/{id}/delete", name="destination_delete",methods={"POST"}, requirements={"id"="\d+"})
     *
     * @param int $id
     * @return Response
     */
    public function deleteAction( int $id) {
        $destinationEntity = $this->destinationService->findOneById( $id );
        $this->destinationService->remove( $destinationEntity );
        $this->addFlash('info', self::SUCCESS_DELETE);
        return $this->redirectToRoute('destination_all');
    }

    /**
     * @Route("destination/{id}/view/{comment}",defaults={"comment"="0"}, name="destination_view", methods={"GET"}, requirements={"id"="\d+", "comment"="\d+"})
     * @param int $id
     * @param int|null $comment
     * @return Response
     */
    public function viewAction(int $id, int $comment = null ) {

        $destinationEntity = $this->destinationService->findOneById( $id );
        $dependence = $this->destinationService->viewDependence( $destinationEntity );

        $commentDestinationEntity = new CommentDestination();
        $formComment = $this->createForm( CommentCreateType::class, $commentDestinationEntity);

        return $this->render( 'destination/view.html.twig', [
            'destination' => $destinationEntity,
            'destinationLiked' => $dependence['destinationLikedEntity'],
            'formComment' => $formComment->createView(),
            'comments' => $this->commentService->getCommentByDestination( $destinationEntity ),
            're' => $comment
        ] );
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("destination/{id}/like", name="destination_like", methods={"GET"}, requirements={"id"="\d+"} )
     *
     */
    public function likeProcess(int $id) {
        $destinationEntity = $this->destinationService->findOneById( $id ) ;

        if( $this->destinationLikedService->like( $destinationEntity, $this->getUser() ) ) {
            $destinationEntity->setCountLiked( $destinationEntity->getCountLiked()+1 );
            $this->destinationService->update( $destinationEntity );
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

        $destinationEntity = $this->destinationService->findOneById( $id ) ;

        if( $this->destinationLikedService->unlike( $destinationEntity, $this->getUser() ) ) {
            $destinationEntity->setCountLiked( $destinationEntity->getCountLiked()-1 );
            $this->destinationService->update( $destinationEntity );
        }
        return $this->redirectToRoute( 'destination_view', ['id' => $id] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("destination/{id}/cemment/new", name="destination_comment_new", methods={"POST"}, requirements={"id"="\d+"})
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function commentProcess(Request $request, $id) {
        $commentEntity = new CommentDestination();
        $form = $this->createForm( CommentCreateType::class, $commentEntity);
        $form->handleRequest( $request );

        if($commentEntity->getIdCommentRe() != null ) {

            $rr = $this->commentService->getCommentById( intval( $commentEntity->getIdCommentRe()) );

            if( intval( $rr->getIdCommentRe() ) )
                return $this->redirectToRoute('destination_view', ['id' => $id]);


                $commentEntity->setCommentsRe( $rr );
            }

        if( $this->commentService->writeComment($commentEntity, $this->destinationService->findOneById( $id )) ) {
                $this->addFlash('info', self::SUCCESS_COMMENT);
            return $this->redirectToRoute('destination_view', ['id' => $id]);
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("commentDestination/{id}/liked", name="liked_comment_destination", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @return RedirectResponse
     */
    public function likeComment( int $id ) {
        $commentEntity = $this->commentService->getCommentById( $id );
        $this->likeCommentService->addLike( $id );

        return $this->redirectToRoute('destination_view', ['id' => $commentEntity->getDestinationId()] );
    }


}
