<?php


namespace WalkAroundBundle\Service\User;
use DateTime;
use WalkAroundBundle\Entity\Role;
use WalkAroundBundle\Service\Encryption\ArgonEncryptionService;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\UserRepository;
use WalkAroundBundle\Service\Role\RoleServiceInterface;

class UserService implements UserServiceInterface
{
    private $userRepository;

    private $roleService;

    private $security;

    private $encryption;

    public function __construct(Security $security, UserRepository $userRepository, RoleServiceInterface $roleService, ArgonEncryptionService $encryption )
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
}