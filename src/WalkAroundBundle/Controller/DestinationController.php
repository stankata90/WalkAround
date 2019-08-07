<?php

namespace WalkAroundBundle\Controller;
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
use WalkAroundBundle\Service\Region\RegionServiceInterface;

class DestinationController extends Controller
{
    private $destinationService;
    private $regionService;
    private $destinationLikedService;
    private $commentService;
    public function __construct(
        DestinationServerInterface $destinationService,
        RegionServiceInterface $regionService,
        DestinationLikedServiceInterface $destinationLikedService,
        CommentDestinationServiceInterface $commentService
    )
    {
        $this->destinationService = $destinationService;
        $this->regionService = $regionService;
        $this->destinationLikedService = $destinationLikedService;
        $this->commentService = $commentService;
    }

    /**
     * @Route("/destinations", name="destination_all", methods={"Get"})
     * @return \Symfony\Component\HttpFoundation\Response
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
            return  $this->redirectToRoute( 'destination_all');
        } catch  ( \Exception $e ) {
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
     * @Route("/destination/{id}/edit", name="destination_edit", methods={"GET"})
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
     * @Route("/destination/{id}/edit", name="destination_edit_process", methods={"POST"})
     *
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
            return  $this->redirectToRoute( 'destination_view',  ['id' => $id] );

        } catch ( \Exception $e ) {

            return  $this->redirectToRoute( 'destination_edit',  ['id' => $id]  );
        }

    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route( "destination/{id}/delete", name="destination_delete",methods={"POST"})
     *
     * @param int $id
     * @return Response
     */
    public function deleteAction( int $id) {
        $destinationEntity = $this->destinationService->findOneById( $id );
        $this->destinationService->remove( $destinationEntity );
        return $this->redirectToRoute('destination_all');
    }

    /**
     * @Route("destination/{id}/view", name="destination_view", methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function viewAction(int $id ) {

        $destinationEntity = $this->destinationService->findOneById( $id );
        $dependence = $this->destinationService->viewDependence( $destinationEntity );

        $commentDestinationEntity = new CommentDestination();
        $formComment = $this->createForm( CommentCreateType::class, $commentDestinationEntity);

        return $this->render( 'destination/view.html.twig', [
            'destination' => $destinationEntity,
            'destinationLiked' => $dependence['destinationLikedEntity'],
            'formComment' => $formComment->createView(),
            'comments' => $this->commentService->getCommentByDestination( $destinationEntity )
        ] );
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("destination/{id}/like", name="destination_like", methods={"GET"} )
     *
     */
    public function likeProcess(int $id) {
        $destinationEntity = $this->destinationService->findOneById( $id ) ;

        if( $this->destinationLikedService->like( $destinationEntity, $this->getUser() ) ) {
            $destinationEntity->setCountLiked( $destinationEntity->getCountLiked()+1 );
            $this->destinationService->update( $destinationEntity );
        }

        return $this->redirectToRoute( 'destination_view', ['id' => $id] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("destination/{id}/unlike", name="destination_unlike", methods={"GET"} )
     *
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
     * @Route("destination/{id}/cemment/new", name="destination_comment_new", methods={"POST"})
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function commentProcess(Request $request, $id) {
        $commentEntity = new CommentDestination();
        $form = $this->createForm( CommentCreateType::class, $commentEntity);
        $form->handleRequest( $request );


        if( $this->commentService->writeComment($commentEntity, $this->destinationService->findOneById( $id )) )
            return $this->redirectToRoute('destination_view', ['id' => $id]);

        return $this->redirectToRoute('homepage');
    }


}
