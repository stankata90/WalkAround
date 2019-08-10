<?php


namespace WalkAroundBundle\Service\CommentEvent;


use WalkAroundBundle\Entity\Event;
use WalkAroundBundle\Entity\EventComment;

interface CommentEventServiceInterface
{
    public function getCommentById( int $id ) :?EventComment;
    public function getCommentsByEvent(Event $event):array;
    public function writeComment(EventComment $comment, Event $event);
    public function deleteComment(EventComment $comment);

}