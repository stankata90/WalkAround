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
use WalkAroundBundle\Form\Comment\CommentDestCreateType;
use WalkAroundBundle\Form\Destination\DestinationCreateType;
use WalkAroundBundle\Form\Destination\DestinationEditType;
use WalkAroundBundle\Service\CommentDestination\CommentDestinationServiceInterface;
use WalkAroundBundle\Service\Destination\DestinationServerInterface;
use WalkAroundBundle\Service\DestinationLiked\DestinationLikedServiceInterface;
use WalkAroundBundle\Service\LikeCommentDestination\LikeCommentDestinationServiceInterface;
use WalkAroundBundle\Service\Region\RegionServiceInterface;
use WalkAroundBundle\Service\User\UserServiceInterface;

class DestinationController extends Controller
{
    const SUCCESS_ADD = 'Success added destination!';
    const SUCCESS_UPDATE = 'Success updated destination!';
    const SUCCESS_DELETE = 'Success deleted destination!';

    private $userService;
    private $destinationService;
    private $regionService;
    private $destinationLikedService;
    private $commentService;
    private $likeCommentService;

    public function __construct(
        UserServiceInterface $userService,
        DestinationServerInterface $destinationService,
        RegionServiceInterface $regionService,
        DestinationLikedServiceInterface $destinationLikedService,
        CommentDestinationServiceInterface $commentService,
        LikeCommentDestinationServiceInterface $likeCommentService
    )
    {
        $this->userService = $userService;
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

            if( $image->getError() == 0 ) {
                $fileName = md5( uniqid() ) . ".". $image->guessExtension();
                $image->move(
                    $this->getParameter( 'destination_directory'),
                    $fileName
                );
                $destinationEntity->setImage( $fileName );
            } else{

                throw new \Symfony\Component\Config\Definition\Exception\Exception('Image max size ' . ( (UploadedFile::getMaxFilesize() / 1024 ) /1024 ) ."mb" );
            }


            if( !$this->destinationService->save( $destinationEntity ) )
                return $this->redirectToRoute('destination_create');

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
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function editAction( $id ) {
        /** @var Destination $destinationEntity */
        $destinationEntity = $this->getDoctrine()->getRepository( Destination::class)->find( intval( $id ) );

        if(!$destinationEntity)
            return $this->goHome();

        if( $this->getUser()->getId() != $destinationEntity->getAddedBy()  and !$this->userService->isAdmin() ) {
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
     * @param $id
     * @return RedirectResponse
     */
    public function editProcess( Request $request, $id) {

        /** @var Destination $destinationEntity*/
        $destinationEntity = $this->getDoctrine()->getRepository(Destination::class)->find( intval( $id ) );

        if(!$destinationEntity)
            return $this->goHome();

        if( $this->getUser()->getId() != $destinationEntity->getAddedBy() and !$this->userService->isAdmin() ) {
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
    public function deleteAction( $id) {
        $destinationEntity = $this->destinationService->findOneById( intval( $id ) );

        if(!$destinationEntity)
            return $this->goHome();


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
    public function viewAction($id, int $comment = null ) {

        $destinationEntity = $this->destinationService->findOneById( intval( $id ) );

        if(!$destinationEntity)
            return $this->goHome();

        $dependence = $this->destinationService->viewDependence( $destinationEntity );

        $commentDestinationEntity = new CommentDestination();
        $formComment = $this->createForm( CommentDestCreateType::class, $commentDestinationEntity);

        return $this->render( 'destination/view.html.twig', [
            'destination' => $destinationEntity,
            'destinationLiked' => $dependence['destinationLikedEntity'],
            'formComment' => $formComment->createView(),
            'comments' => $this->commentService->getCommentByDestination( $destinationEntity ),
            're' => $comment
        ] );
    }

    function goHome() {
        return $this->redirectToRoute('destination_all');
    }

}
