<?php


namespace WalkAroundBundle\Service\User;
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

        $user->setAddedOn( new \DateTime('now'));

        return $this->userRepository->insert( $user );
        // TODO: Implement save() method.
    }

    public function findOneByEmail(string $email): ?User
    {
        // TODO: Implement findOneByEmail() method.
    }

    public function findOneById(int $id): ?User
    {
        // TODO: Implement findOneById() method.
    }

    public function findOne(User $user): ?User
    {
        // TODO: Implement findOne() method.
    }

    public function currentUser(): ?User
    {
        // TODO: Implement currentUser() method.
    }
}