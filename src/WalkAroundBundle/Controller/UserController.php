<?php

namespace WalkAroundBundle\Controller;

use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Form\User\UserRegisterType;
use WalkAroundBundle\Service\User\UserServiceInterface;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserServiceInterface $userService )
    {
        $this->userService = $userService;
    }



    /**
     * @Route( "user/login", name="user_login")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction( Request $request )
    {
        return $this->render('user/login.html.twig' );
    }

    /**
     * @Route( "user/logout", name="user_logout")
     *
     * @return Response
     */
    public function logoutAction( Request $request )
    {
        return $this->render('user/login.html.twig' );
    }

    /**
     * @Route( "user/register", name="user_register", methods={"GET"} )
     *
     * @param Request $request
     * @return Response
     */
    public function registerAction( Request $request ) {

        $this->addFlash( 'info', 'Success registration !');

        return $this->render( 'user/register.html.twig', ['form' => $this->createForm( UserRegisterType::class )->createView() ] );

    }

    /**
     * @Route( "user/register", name="user_register_process", methods={"POST","GET"} )
     *
     * @param Request $request
     * @return Response
     */
    public function registerProcess(Request $request ) {
            try {
                $userEntity = new User();
                $form = $this->createForm( UserRegisterType::class, $userEntity );
                $form->handleRequest( $request );
                $this->userService->save( $userEntity );
                return $this->redirectToRoute( "homepage");
            } catch  ( \Exception $e ) {
                $this->addFlash('error', $e->getMessage() );
                return $this->render( 'user/register.html.twig', ['form' => $form->createView() ] );
            }
    }

    /**
     * @Route( "user/profile", name="user_profile", methods={"GET"} )
     *
     * @param Request $request
     * @return Response
     */
    public function profileAction(Request $request ) {
        try {
            $userEntity = new User();
            $form = $this->createForm( UserRegisterType::class, $userEntity );
            $form->handleRequest( $request );
            $this->userService->save( $userEntity );
            return $this->redirectToRoute( "homepage");
        } catch  ( \Exception $e ) {
            $this->addFlash('error', $e->getMessage() );
            return $this->render( 'user/register.html.twig', ['form' => $form->createView() ] );
        }
    }
}
