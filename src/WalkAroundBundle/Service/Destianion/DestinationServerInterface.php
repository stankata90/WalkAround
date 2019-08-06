<?php


namespace WalkAroundBundle\Service\Destianion;


use WalkAroundBundle\Entity\Destination;

interface DestinationServerInterface
{
    public function save( Destination $destination ):bool;
    public function update( Destination $destination) :bool;
    public function remove( Destination $destination ): bool;
    public function findOne( Destination $destination) :?Destination;
    public function findOneById( int $id ) :?Destination;
    public function findAll();
}