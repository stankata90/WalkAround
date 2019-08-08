<?php


namespace WalkAroundBundle\Service\User;
use DateTime;
use Exception;
use Symfony\Component\Form\FormInterface;
use WalkAroundBundle\Entity\Role;
use WalkAroundBundle\Form\User\UserRegisterType;
use WalkAroundBundle\Service\Encryption\ArgonEncryptionService;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\UserRepository;
use WalkAroundBundle\Service\Role\RoleServiceInterface;

class UserService implements UserServiceInterface
{
    const AGE_REQ = "Age must be between 10 and 100, only number";
    const NAME_REQ = "Name must be min 3 and max 50 letters";
    const SEX_REQ = "Invalid sex";
    const PASS_REQ = "Password must be between 4 and 20, letters and digits";

    private $userRepository;

    private $roleService;

    private $security;

    private $encryption;

    public function __construct(
        Security $security,
        UserRepository $userRepository,
        RoleServiceInterface $roleService,
        ArgonEncryptionService $encryption
    )
    {
        $this->userRepository = $userRepository;
        $this->roleService = $roleService;
        $this->security = $security;
        $this->encryption = $encryption;
    }

    public function save(User $user): bool
    {

        $passwordHash = $this->encryption->hash( $user->getPassword() );
        $user->setPassword( $passwordHash );

        /** @var Role $userRole */
        $userRole = $this->roleService->findOneByName( 'USER' );


        $user->addRole( $userRole );


        $user->setAddedOn( new DateTime('now'));

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
    public function registerProcess($userController, $request, &$form)
    {
        $userEntity = new User();

        /** @var FormInterface $form */
        $form = $userController->createForm( UserRegisterType::class, $userEntity );
        $arrValidate = $request->request->get("user");
        $form->handleRequest( $request );

        $this->verifyName( $arrValidate['fullName'] );
        $this->verifyAge( $arrValidate['age'] );
        $this->verifySex( $arrValidate['sex'] );
        $this->verifyPassword( $arrValidate['password']['first']);

        $fileName = md5( uniqid() ) . ".png";
        copy($userController->getParameter('user_directory')."/avatar.png", $userController->getParameter('user_directory') ."/". $fileName);
        $userEntity->setImage($fileName);
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
    public function verifyName( $name ):bool {
        if( !ctype_alpha($name) OR mb_strlen( $name ) < 3 OR mb_strlen( $name ) > 50 ) {
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
        if( !ctype_digit($age) OR $age > 100 OR  $age < 10 ) {
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


}