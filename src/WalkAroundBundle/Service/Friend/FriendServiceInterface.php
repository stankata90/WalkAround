<?php


namespace WalkAroundBundle\Service\Friend;

use WalkAroundBundle\Entity\User;

interface FriendServiceInterface
{
    public function findAll();
    public function findFriend( User $friend);
    public function sendInvite( User $friend );
    public function acceptInvite( User $friend);
    public function removeFriend(User $friend );

}