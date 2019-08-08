<?php


namespace WalkAroundBundle\Service\LikeCommentDestination;


use WalkAroundBundle\Entity\CommentDestinationLiked;

interface LikeCommentDestinationServiceInterface
{
    public function add(CommentDestinationLiked $like );

    public function remove( CommentDestinationLiked $like );

    public function finOne( int $likeId );

    public function addLike(int $comId );
}