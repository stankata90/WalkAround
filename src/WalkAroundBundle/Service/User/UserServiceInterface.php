<?php


namespace WalkAroundBundle\Service\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use WalkAroundBundle\Entity\User;

interface UserServiceInterface
{
    public function registerProcess( Controller $controller, Request $request, &$form );
    public function editProcess(Controller $contr, Request $request, &$form );

    public function createUser(User $user ) : bool;
    public function updateUser(User $newUser) : bool;

    public function findOneById( int $id ) : ?object;
    public function findOneByEmail( string $email ) : ?object;
    public function findAll();

    public function listAll( int $page, &$findPages ):?array;

    public function verifyEmail( $email );
    public function verifyName( $name ):bool;
    public function verifyAge( $age );
    public function verifySex( $sex );
    public function verifyPassword( $password );

    public function isAdmin():bool;
    public function install();

    public function deleteImage(User $user, $controller );
    public function createImage( UploadedFile $image, $controller );

}