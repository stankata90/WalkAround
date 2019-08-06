<?php

namespace WalkAroundBundle\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Form\Destination\DestinationCreateType;
use WalkAroundBundle\Form\Destination\DestinationEditType;
use WalkAroundBundle\Service\Destianion\DestinationServerInterface;
use WalkAroundBundle\Service\Region\RegionServiceInterface;

class DestinationController extends Controller
{
    private $destinationService;
    private $regionService;
    public function __construct(
        DestinationServerInterface $destinationService,
        RegionServiceInterface $regionService
)
    {
        $this->destinationService = $destinationService;
        $this->regionService = $regionService;
    }

    /**
     * @Route("destination/all", name="destination_all", methods={"Get"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        return $this->render('destination/all.html.twig', array('destinations' => $this->destinationService->findAll() ));
    }

    /**
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
     * @Route("/destination/{id}/edit", name="destination_edit", methods={"GET"})
     *
     */
    public function editAction( int $id ) {
        return $this->render(
            'destination/edit.html.twig',
            [
                'form' => $this->createForm( DestinationEditType::class )->createView( ),
                'destination' => $this->getDoctrine()->getRepository( Destination::class)->find( $id),
                'regions' =>$this->regionService->getAll()
            ]
        );
    }

    /**
     * @Route("/destination/{id}/edit", name="destination_edit_process", methods={"POST"})
     *
     *
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editProcess( Request $request, int $id) {


        /** @var Destination $destinationEntity*/
        $destinationEntity = $this->getDoctrine()->getRepository(Destination::class)->find( $id );
        $form = $this->createForm( DestinationEditType::class, $destinationEntity);
        $form->handleRequest(  $request );

        try{
            /** @var UploadedFile $image */
            $image = $form['image']->getData();
            if(  $image AND !$image->getError() ) {
               $fileName = md5( uniqid() ) . ".". $image->guessExtension();
               $image->move(
                   $this->getParameter( 'destination_directory'),
                   $fileName
               );

               $destinationEntity->setImage( $fileName );
           }

            $this->destinationService->update( $destinationEntity );
            return  $this->redirectToRoute( 'destination_view',  ['id' => $id] );

        } catch ( \Exception $e ) {

            return  $this->redirectToRoute( 'destination_edit',  ['id' => $id]  );
        }

    }

    /**
     * @Route( "destination/{id}/delete", name="destination_delete",methods={"GET"})
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
     * @Route( "destination/{id}/delete", name="destination_delete_process",methods={"GET"})
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProcess( int $id ) {


    }
    /**
     * @Route("destination/{id}/view", name="destination_view", methods={"GET"})
     * @return Response
     */
    public function viewAction(int $id ) {

        $destinationEntity = $this->destinationService->findOneById( $id );
        return $this->render( 'destination/view.html.twig', ['destination' => $destinationEntity] );
    }
}
