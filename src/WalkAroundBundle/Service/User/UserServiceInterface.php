<?php


namespace WalkAroundBundle\Service\User;

use Symfony\Component\HttpFoundation\Request;
use WalkAroundBundle\Controller\UserController;
use WalkAroundBundle\Entity\User;

interface UserServiceInterface
{
    public function save( User $user ) : bool;
    public function updateProfile( string $currentPassword, User $newUser) : bool;
    public function findOneById( int $id ) : ?object;
    public function findOneByEmail( string $email ) : ?object;
    public function findAll();

    public function registerProcess( UserController $controller, Request $request, &$form );

    public function verifyEmail( $email ):bool;
    public function verifyName( $name );
    public function verifyAge( $age ):bool;
    public function verifySex( $sex ):bool;
    public function verifyPassword( $password ):bool;

    public function isAdmin():bool;
    public function install();
}