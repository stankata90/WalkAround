<?php


namespace WalkAroundBundle\Service\User;
use DateTime;
use Exception;
use WalkAroundBundle\Entity\Role;
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
     * @param $user
     * @throws Exception
     */
    public function checkRegisterForm($user)
    {

        if( !ctype_alpha( $user['fullName'] ) OR ( mb_strlen( $user['fullName'] ) < 3 OR mb_strlen( $user['fullName'] ) > 50 ) ) {
            throw new Exception( self::NAME_REQ );
        }

        if( !ctype_digit( $user['age']) or ( $user['age'] > 100 &&  $user['age'] < 10 ) ) {
            throw new Exception( self::AGE_REQ );
        }

        if( !( $user['sex'] == "male" OR $user['sex'] == "female" )) {
            throw new Exception( self::SEX_REQ );
        }

        if( !ctype_alnum($user['password'] ) or !( mb_strlen( $user['password'] ) > 20  OR mb_strlen( $user['password'] ) < 4 )) {
            throw new Exception( self::PASS_REQ );
        }

        var_dump( $user);
    }
}