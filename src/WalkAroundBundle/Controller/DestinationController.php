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
                'form' => $this->createForm( DestinationEditType::class )->createView()
            ]
        );
    }
    /**
     * @Route("destination/{id}/view", name="destination_view", methods={"GET"})
     * @return Response
     */
    public function viewAction(int $id ) {

//        var_dump( $id);
//        exit();
        $destinationEntity = $this->destinationService->findOneById( $id );
        return $this->render( 'destination/view.html.twig', ['destination' => $destinationEntity] );
    }
}
