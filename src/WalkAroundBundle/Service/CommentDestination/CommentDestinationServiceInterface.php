<?php


namespace WalkAroundBundle\Service\CommentDestination;


use WalkAroundBundle\Entity\CommentDestination;
use WalkAroundBundle\Entity\Destination;
use WalkAroundBundle\Entity\User;

interface CommentDestinationServiceInterface
{
    public function writeComment( CommentDestination $comment, Destination $destination);
    public function removeComment( CommentDestination $comment ) :bool;
    public function removeCommentsByDestination(Destination $destination) :bool;
    public function getCommentById( int $id ) :?CommentDestination;
    public function getCommentByAuthor(User $author) :?CommentDestination;
    public function getCommentByAuthorId( int $authorId ) :?CommentDestination;
    public function getCommentByDestination( Destination $destination);
    public function getCommentsByDestination( Destination $destination );
    public function getCommentByDestinationId( int $id ) :?CommentDestination;
    public function geCommentByReId( int $reId ):?CommentDestination;
    public function likeComment( CommentDestination $comment ):bool;
    public function unlikeComment( CommentDestination $coment ):bool;
}