<?php

namespace WalkAroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;

class UserController extends Controller
{
    public function indexAction($name)
    {
        return new Response( 'aaaa' );
    }
}
