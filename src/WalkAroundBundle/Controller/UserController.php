<?php


namespace WalkAroundBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Check;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Form\User\UserEditType;
use WalkAroundBundle\Form\User\UserRegisterType;
use WalkAroundBundle\Service\User\UserServiceInterface;

class UserController extends Controller
{
    const SUCCESS_REG = "Success register, please login !";
    private $userService;
    private $security;

    public function __construct( UserServiceInterface $userService, Security $security )
    {
        $this->security = $security;
        $this->userService = $userService;
    }


    /**
     * @Route( "user/login", name="user_login")
     *
     * @return Response
     */
    public function loginAction(  )
    {
        return $this->render('user/login.html.twig' );
    }

    /**
     * @Check("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route( "user/logout", name="user_logout")
     *
     * @return Response
     */
    public function logoutAction( )
    {

        return $this->render('user/login.html.twig' );
    }

    /**
     * @Route( "user/register", name="user_register", methods={"GET"} )

     * @return Response
     */
    public function registerAction( ) {
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
                $fileName = md5( uniqid() ) . ".png";
                copy($this->getParameter('user_directory')."/avatar.png", $this->getParameter('user_directory') ."/". $fileName);
                $userEntity->setImage($fileName);
                $this->userService->save( $userEntity );
                $this->addFlash('info', self::SUCCESS_REG);
                return $this->redirectToRoute( "homepage");
            } catch  ( Exception $e ) {
                $this->addFlash('error', $e->getMessage() );
                return $this->render( 'user/register.html.twig', ['form' => $form->createView() ] );
            }
    }

    /**
     * @Check("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route( "user/myprofile", name="my_profile", methods={"GET"} )
     *
     * @param Request $request
     * @return Response
     */
    public function myProfileAction(Request $request ) {

        /** @var User $cuerrentUser|$userEntity */
        $currentUser = $this->getUser();
        $userEntity = $this->getDoctrine()->getRepository( User::class )->find( $currentUser->getId() );

        $form = $this->createForm( UserEditType::class, $userEntity );
        $form->handleRequest( $request );

        return $this->render( 'user/myProfile.html.twig',
            [
                'form' => $form->createView(),
                'user' => $userEntity
            ] );
    }

    /**
     * @Check("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route( "user/myprofile", name="my_profile_process", methods={"POST"} )
     * @param Request $request
     * @return RedirectResponse
     */
    public function editProfileProcess( Request $request )
    {
        /**@var User $userEntity */
        $userEntity = $this->security->getUser();
        $oldImage = $userEntity->getImage();
        $currentPass =  $userEntity->getPassword();
        $form = $this->createForm(UserEditType::class, $userEntity);
        $form->handleRequest($request);

        /** @var UploadedFile $image */
        $image = $form['image']->getData();

        if(  $image !== NULL AND !$image->getError() ) {

            $fileName = md5( uniqid() ) . ".". $image->guessExtension();
            $image->move(
                $this->getParameter( 'user_directory'),
                $fileName
            );

            $file =$this->getParameter( 'user_directory') ."/". $oldImage;

            if( file_exists( $file ) && $oldImage  )
                unlink( $file );

            $userEntity->setImage( $fileName );
        } else {
            $userEntity->setImage(  $oldImage );
        }

        $this->userService->updateProfile($currentPass, $userEntity);

        $this->addFlash('into', 'Success edit your profile!');
        return $this->redirectToRoute( 'my_profile');
    }

    /**
     * @Route("/users", name="users" )
     */
    public function allAction() {
        /** @var User[] $arrUser */
        $arrUser = $this->userService->findAll();

        return $this->render( 'user/all.html.twig', ['users' => $arrUser]);
    }

    /**
     * @Check("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route( "/user/{id}/view", name="user_profile", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function userAction( int $id ) {
        /** @var User $user */
        $user = $this->userService->findOneById( $id );

        if( !$user )
          return $this->redirectToRoute('homepage');


        return $this->render('user/user.html.twig', ['user' =>$user] );
    }
}
