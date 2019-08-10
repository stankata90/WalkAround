<?php


namespace WalkAroundBundle\Service\Event;

use Symfony\Component\HttpFoundation\Request;
use WalkAroundBundle\Controller\EventController;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\Event;
use WalkAroundBundle\Entity\EventUser;

interface EventServiceInterface
{
    public function createProcess( EventController $controller, Request $request, &$form, Destination $destEntity );
    public function acceptProcess( EventUser $eventUser);
    public function leaveProcess( EventUser $eventUser );
    public function endProcess( Event $event );
    public function dropProcess( Event $event);

    public function findOneById( int $id ):?object;
    public function findById( int $id );
    public function findByDestination( Destination $destination );

    public function findInvitedById($id );
    public function findInvitedUsers( Event $event ) :?array ;
    public function isCompleted( Event $event );


}