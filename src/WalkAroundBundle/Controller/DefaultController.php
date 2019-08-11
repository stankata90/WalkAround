<?php

namespace WalkAroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Service\Destination\DestinationServerInterface;

class DefaultController extends Controller
{
    private $destinationService;

    function __construct( DestinationServerInterface $destination )
    {
        $this->destinationService = $destination;
    }


    /**
     * @Route("/{page}", defaults={"page"="0"}, requirements={"page"="\d+"}, name="homepage")
     * @param int $page
     * @return Response
     */
    public function indexAction( int $page )
    {

        return $this->render('destination/all.html.twig', array('destinations' => $this->destinationService->listAll( $page, $findPages ), 'intPages' => $findPages ));
    }
}
