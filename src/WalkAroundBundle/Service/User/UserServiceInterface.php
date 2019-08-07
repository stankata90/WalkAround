<?php


namespace WalkAroundBundle\Service\User;


use WalkAroundBundle\Entity\User;

interface UserServiceInterface
{
    public function save( User $user ) : bool;
    public function updateProfile( string $currentPassword, User $newUser) : bool;
    public function findOneById( int $id ) : ?object;
    public function findAll();
}