<?php


namespace SoftUniBlogBundle\Service\User;


use SoftUniBlogBundle\Entity\User;

interface UserInterface
{
    public function save( User $user ) : bool;
    public function findOneByEmail( string $email ) : ?User;
    public function findOneById( int $id ) : ?User;
    public function findOne( User $user ) : ?User;
    public function currentUser() : ?User;
}