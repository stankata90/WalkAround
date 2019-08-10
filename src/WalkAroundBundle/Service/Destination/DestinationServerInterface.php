<?php


namespace WalkAroundBundle\Service\Destination;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use WalkAroundBundle\Controller\DestinationController;
use WalkAroundBundle\Entity\Destination;

interface DestinationServerInterface
{
    public function createProcess(Controller $contr, Request $request, Destination &$destEntity ):bool;
    public function updateProcess(Destination $destination, Controller $contr, Request $request ) :bool;
    public function removeProcess(Destination $destination, DestinationController $controller ): bool;

    public function findAll();
    public function findOne( Destination $destination) :?Destination;
    public function findOneById( int $id ) :?Destination;
    public function findDestinationEvents(Destination $destination);

    public function update(Destination $destination);

    public function addSeenCount( Destination $destination);
    public function viewDependence( Destination $destination );

    public function deleteImage( Destination $destination, Controller $contr );
    public function createImage( UploadedFile $image, Controller $contr );
}