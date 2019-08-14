<?php


namespace WalkAroundBundle\Service\Message;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WalkAroundBundle\Entity\Mail;
use WalkAroundBundle\Entity\User;

interface MailServiceInterface
{
    public function sendMessage(Mail $message):bool ;
    public function createMessage(Controller $controller, Request $request, User $user ):bool;
    public function removeMessage(Mail $message):bool;
    public function deleteMessage(Mail $message):bool;
    public function seenMessage(Mail $message):bool;
    public function readMessage(Mail $message):?object;
    public function getMessage(Mail $message):?object;
    public function getMessageById(int $messageId):?object;
    public function getMessageByAuthor(User $user) :?object;
    public function getMessageByAuthorId(int $authorId ) :?object;
    public function getMessageByRecipient(User $recipient ) :?object;
    public function getMessageByRecipientId( int $recipientId ) :object;
    public function getInboxMessage( User $user ) :array;
    public function getOutboxMessage( User $user ) :array;
}