<?php


namespace WalkAroundBundle\Service\User;


use WalkAroundBundle\Entity\User;

interface UserServiceInterface
{
    public function save( User $user ) : bool;
    public function findOneByEmail( string $email ) : ?User;
    public function findOneById( int $id ) : ?User;
    public function findOne( User $user ) : ?User;
    public function currentUser() : ?User;
}