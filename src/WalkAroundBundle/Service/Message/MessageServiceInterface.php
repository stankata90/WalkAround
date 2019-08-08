<?php


namespace WalkAroundBundle\Service\Message;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use WalkAroundBundle\Entity\Message;
use WalkAroundBundle\Entity\User;

interface MessageServiceInterface
{
    public function sendMessage(Message $message):bool ;
    public function createMessage(FormInterface $form, Message $message, Request $request):bool;
    public function removeMessage(Message $message):bool;
    public function deleteMessage(Message $message):bool;
    public function seenMessage(Message $message):bool;
    public function readMessage(Message $message):?object;
    public function getMessage(Message $message):?object;
    public function getMessageById(int $messageId):?object;
    public function getMessageByAuthor(User $user) :?object;
    public function getMessageByAuthorId(int $authorId ) :?object;
    public function getMessageByRecipient(User $recipient ) :?object;
    public function getMessageByRecipientId( int $recipientId ) :object;
    public function getInboxMessage( User $user ) :array;
    public function getOutboxMessage( User $user ) :array;
}