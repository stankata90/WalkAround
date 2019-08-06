<?php


namespace WalkAroundBundle\Service\Role;


use WalkAroundBundle\Entity\Role;
use WalkAroundBundle\Repository\RoleRepository;

class RoleService implements RoleServiceInterface
{

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    public function __construct( RoleRepository $roleRepository )
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param string $name
     * @return Role|null|object
     */
    public function findOneByName(string $name): ?Role
    {
        return  $this->roleRepository->findOneBy(['name' => $name]);
    }

    /**
     * @param int $id
     * @return Role|null|object
     */
    public function findOneById(int $id): ?Role
    {
        return  $this->roleRepository->find( $id );
    }

    /**
     * @param Role $role
     * @return Role|null|object
     */
    public function findOne(Role $role): ?Role
    {
        return $this->roleRepository->find( $role );
    }
}