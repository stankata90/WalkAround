<?php


namespace WalkAroundBundle\Service\User;


use WalkAroundBundle\Entity\User;

class UserService implements UserServiceInterface
{

    public function save(User $user): bool
    {
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