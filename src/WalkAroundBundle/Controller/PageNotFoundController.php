<?php

namespace WalkAroundBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageNotFoundController extends Controller
{
    public function pageNotFoundAction()
    {
        return $this->render('error/404.html.twig');
    }
}
