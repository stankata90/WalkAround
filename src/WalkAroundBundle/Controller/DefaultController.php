<?php

namespace WalkAroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('destination/all.html.twig', array('destinations' => $this->destinationService->findAll() ));
    }
}
