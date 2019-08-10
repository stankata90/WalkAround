<?php


namespace WalkAroundBundle\Service\User;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use WalkAroundBundle\Controller\DestinationController;
use WalkAroundBundle\Controller\UserController;
use WalkAroundBundle\Entity\Region;
use WalkAroundBundle\Entity\Role;
use WalkAroundBundle\Form\User\UserEditType;
use WalkAroundBundle\Form\User\UserRegisterType;
use WalkAroundBundle\Repository\RegionRepository;
use WalkAroundBundle\Repository\RoleRepository;
use WalkAroundBundle\Service\Encryption\ArgonEncryptionService;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\UserRepository;
use WalkAroundBundle\Service\Role\RoleServiceInterface;

class UserService implements UserServiceInterface
{
    const ERROR_EMAIL_USED = "Email already registered !";
    const AGE_REQ = "Age must be between 10 and 100, only number";
    const NAME_REQ = "Name must be min 3 and max 50 letters";
    const SEX_REQ = "Invalid sex";
    const PASS_REQ = "Password must be between 4 and 20, letters and digits";

    private $userRepository;
    private $roleRepository;
    private $regionRepository;

    private $roleService;

    private $security;

    private $encryption;

    public function __construct(
        Security $security,
        UserRepository $userRepository,
        RoleServiceInterface $roleService,
        ArgonEncryptionService $encryption,
        RoleRepository $roleRepository,
        RegionRepository $regionRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->roleService = $roleService;
        $this->security = $security;
        $this->encryption = $encryption;
        $this->roleRepository = $roleRepository;
        $this->regionRepository = $regionRepository;
    }

    /**
     * @param Controller|UserController $userController
     * @param $request
     * @param $form
     * @return mixed
     * @throws Exception
     */
    public function registerProcess(Controller $userController, $request, &$form)
    {
        $userEntity = new User();

        /** @var FormInterface $form */
        $form = $userController->createForm( UserRegisterType::class, $userEntity );
        $arrValidate = $request->request->get("user");
        $form->handleRequest( $request );

        $this->verifyEmail( $userEntity->getEmail() );
        $this->verifyName( $userEntity->getFullName() );
        $this->verifyAge( intval( $userEntity->getAge() ) );
        $this->verifySex($userEntity->getSex() );

        $this->verifyPassword( $arrValidate['password']['first']);
        $fileName = md5( uniqid() ) . ".png";
        copy($userController->getParameter('user_directory')."/avatar.png", $userController->getParameter('user_directory') ."/". $fileName);
        $userEntity->setImage($fileName);

        $passwordHash = $this->encryption->hash( $userEntity->getPassword() );
        $userEntity->setPassword( $passwordHash );

        /** @var Role $userRole */
        if( $this->install() ) {
            $adminRole = $this->roleService->findOneByName( 'ROLE_ADMIN' );
            $userEntity->addRole( $adminRole );

        }

        $userRole = $this->roleService->findOneByName( 'ROLE_USER' );
        $userEntity->addRole( $userRole );

        $userEntity->setAddedOn( new DateTime('now'));

        $this->createUser( $userEntity );

        return true;
    }

    /**
     * @param Controller $controller
     * @param Request $request
     * @param $form
     * @throws Exception
     */
    public function editProcess(Controller $controller, Request $request, &$form)
    {

        /**@var User $userEntity */
        $userEntity = $this->security->getUser();
        $oldImg = $userEntity->getImage();
        $oldProfile = new User();
        $oldProfile
            ->setEmail( $userEntity->getEmail() )
            ->setImage( $userEntity->getImage() )
        ;

        $currentPass =  $userEntity->getPassword();

        /** @var FormInterface $form */
        $form = $controller->createForm(UserEditType::class, $userEntity);
        $form->handleRequest($request);

        if( $oldProfile->getEmail() != $userEntity->getEmail() )
            $this->verifyEmail( $userEntity->getEmail() );

        $this->verifyName( $userEntity->getFullName() );

        $this->verifyAge( intval( $userEntity->getAge() ) );

        $this->verifySex($userEntity->getSex() );

        $arrValidate = $request->request->get("user");
        $this->verifyPassword( $arrValidate['password']['first']);

        if( $userEntity->getPassword() !== Null  && $userEntity->getPassword() !== $currentPass && !$this->encryption->verify($userEntity->getPassword(), $currentPass) ) {
            $userEntity->setPassword( $this->encryption->hash( $userEntity->getPassword() ) );
        } else {
            $userEntity->setPassword($currentPass);
        }

        /** @var UploadedFile $image */
        $image = $form['image']->getData();


        if( $request->files->get('user')['image'] and !$image->getError() == 0 )
            throw new Exception('Image max size ' . ( (UploadedFile::getMaxFilesize() / 1024 ) /1024 ) ."mb" );


        if( $request->files->get('user')['image'] != null  ) {

            $fileName = $this->createImage( $image, $controller );
            $this->deleteImage( $oldProfile, $controller );
            $userEntity->setImage( $fileName );

        } else {

            $userEntity->setImage( $oldProfile->getImage() );
        }


        $this->updateUser($userEntity);
    }


    public function createUser(User $user): bool
    {

        return $this->userRepository->insert( $user );
    }

    /**
     * @param string $currentPassword
     * @param User $newUser
     * @return bool|void
     */
    public function updateUser(User $newUser ) : bool {

        return $this->userRepository->update( $newUser );
    }

    /**
     * @param int $id
     * @return User|null|object
     */
    public function findOneById(int $id): ?object
    {
        return  $this->userRepository->findOneBy( ['id' => $id ]);
    }

    /**
     * @param string $email
     * @return object|null
     */
    public function findOneByEmail( string $email ) : ?object {

        return $this->userRepository->findOneBy(['email' => $email]);
    }

    /**
     * @return User[]|null|object
     */
    public function findAll()
    {

        return $this->userRepository->findAll();
    }

    /**
     * @param $email
     * @return bool
     * @throws Exception
     */
    public function verifyEmail($email)
    {
        if( $this->findOneByEmail( $email ) )
            throw new Exception('Email already registered !');

        return true;
    }

    /**
     * @param $name
     * @return bool
     * @throws Exception
     */
    public function verifyName( $name ):bool {
        $re = '/^[a-zA-Z]?[а-яА-Я]?[\D]+$/';

        if( preg_match($re, $name) == 0 ) {
            throw new Exception( self::NAME_REQ );
        }

        return true;
    }

    /**
     * @param $age
     * @return bool
     * @throws Exception
     */
    public function verifyAge( $age ):bool{

        if(  $age > 100 OR  $age < 10 ) {
            throw new Exception( self::AGE_REQ );
        }
        return true;
    }

    /**
     * @param $sex
     * @return bool
     * @throws Exception
     */
    public function verifySex( $sex ):bool {
        if( $sex != "male" AND $sex != "female" ) {
            throw new Exception( self::SEX_REQ );
        }
        return true;
    }

    /**
     * @param $password
     * @return bool
     * @throws Exception
     */
    public function verifyPassword( $password ):bool {
        if( !ctype_digit($password) OR mb_strlen( $password ) > 20 OR mb_strlen( $password ) < 4 ) {
            throw new Exception( self::PASS_REQ );
        }
        return true;
    }


    public function isAdmin(): bool
    {
        return in_array( 'ROLE_ADMIN', $this->security->getUser()->getRoles()) ;
    }

    public function install()
    {
       if( !$this->userRepository->findAll() ){

           $roles = ['ROLE_USER', 'ROLE_ADMIN'];

           foreach ( $roles as $rolName ) {
               $rol = new Role();
                    $rol->setName( $rolName );

                $this->roleRepository->insert( $rol );
           }
           $regions = ['North Western', 'North Central', 'North Eastern', 'South Eastern', 'South Western'];

           foreach ( $regions as $regionName ) {
               $region = new Region();
               $region->setName( $regionName );

               $this->regionRepository->insert( $region );
           }

           return true;
       }

       return false;
    }


    /**
     * @param User $user
     * @param DestinationController|Controller $controller
     * @return bool
     */
    public function deleteImage(User $user, $controller ) {
        $image = $controller->getParameter('user_directory').'/'.$user->getImage();

        if(file_exists( $image ))
            if( unlink( $image ))
                return true;

        return false;
    }

    /**
     * @param UploadedFile $image
     * @param DestinationController|Controller $controller
     * @return bool
     * @throws Exception
     */
    public function createImage( UploadedFile $image, $controller ) {

        $fileName = md5( uniqid() ) . ".". $image->guessExtension();
        try {
            $image->move(
                $controller->getParameter('user_directory'),
                $fileName
            );

            return $fileName;
        } catch ( FileException $e ) {

            throw new Exception( $e->getMessage() );
        }
    }

}