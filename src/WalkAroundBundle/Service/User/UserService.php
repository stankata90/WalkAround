<?php


namespace WalkAroundBundle\Service\User;
use DateTime;
use Exception;
use Symfony\Component\Form\FormInterface;
use WalkAroundBundle\Controller\UserController;
use WalkAroundBundle\Entity\Region;
use WalkAroundBundle\Entity\Role;
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

    public function save(User $user): bool
    {

        return $this->userRepository->insert( $user );
    }

    /**
     * @param string $currentPassword
     * @param User $newUser
     * @return bool|void
     */
    public function updateProfile( ?string $currentPassword, User $newUser ) : bool {

        if( $newUser->getPassword() !== Null  && $newUser->getPassword() !== $currentPassword && !$this->encryption->verify($newUser->getPassword(), $currentPassword) ) {
            $newUser->setPassword( $this->encryption->hash( $newUser->getPassword() ) );
        } else {
            $newUser->setPassword($currentPassword);
        }

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
     * @param $userController
     * @param $request
     * @param $form
     * @return mixed
     * @throws Exception
     */
    public function registerProcess(UserController $userController, $request, &$form)
    {
        $userEntity = new User();

        /** @var FormInterface $form */
        $form = $userController->createForm( UserRegisterType::class, $userEntity );
        $arrValidate = $request->request->get("user");
        $form->handleRequest( $request );

        if( $this->findOneByEmail( $userEntity->getEmail() ) )
            throw new Exception('Email already registered !');

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

        $this->save( $userEntity );

        return true;
    }

    public function verifyEmail($email): bool
    {
        // TODO: Implement verifyEmail() method.
    }

    /**
     * @param $name
     * @return bool
     * @throws Exception
     */
    public function verifyName( $name ) {
        $re = '/^[a-zA-Z]?[а-яА-Я]?[\D]+$/';

        if( preg_match($re, $name) == 0 ) {
            throw new Exception( self::NAME_REQ );
        }
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
}