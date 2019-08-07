<?php


namespace WalkAroundBundle\Service\DestinationLiked;


use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\DestinationLiked;
use WalkAroundBundle\Entity\User;

interface DestinationLikedServiceInterface
{
    public function like(Destination $destination, User $user);
    public function unlike(Destination $destination, User $user);
    public function findLike( Destination $destination, User $user );
    public function removeLikesByDestination( Destination $destination );
}