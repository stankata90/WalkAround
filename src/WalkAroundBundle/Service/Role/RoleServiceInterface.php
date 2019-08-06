<?php


namespace WalkAroundBundle\Service\Role;


use WalkAroundBundle\Entity\Role;

interface RoleServiceInterface
{
    public function findOneByName( string $name ) : ?Role;
    public function findOneById( int $id ) : ?Role;
    public function findOne( Role $role ) : ?Role;
}