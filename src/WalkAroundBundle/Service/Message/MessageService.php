<?php


namespace WalkAroundBundle\Service\Message;

use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\Message;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\MessageRepository;
use WalkAroundBundle\Service\User\UserServiceInterface;

class MessageService implements MessageServiceInterface
{
    private $messageRepository;
    /** @var User */
    private $currentUser;
    private $security;
    private $userService;
    function __construct(MessageRepository $messageRepository, Security $security, UserServiceInterface $userService )
    {
        $this->messageRepository = $messageRepository;
        $this->security = $security;
        $this->userService = $userService;
    }

    public function sendMessage( Message $message ) :bool
    {
        if( $this->messageRepository->insert( $message ) )
            return true;

        return false;
    }

    public function createMessage(FormInterface $form, Message $message, Request $request ): bool
    {
        /** @var User $formUser */
        $formUser = $this->userService->findOneById( $message->getForId() );
        $this->currentUser = $this->security->getUser();
        $form->handleRequest( $request );
        $message
            ->setFromUser( $this->currentUser )
            ->setForUser( $formUser)
            ->setAddedOn( new DateTime('now'));

        return $this->sendMessage( $message );
    }

    public function removeMessage(Message $message): bool
    {
        if( $this->messageRepository->delete($message) )
            return true;

        return false;
    }

    public function deleteMessage(Message $message): bool
    {
        return $this->removeMessage( $message );
    }

    public function seenMessage(Message $message): bool
    {
        if( $this->messageRepository->update( $message->setSeenOn( new DateTime('now') ) ) )
            return true;

        return false;
    }

    public function readMessage(Message $message): ?object
    {
         $this->seenMessage( $message );
         return $this->getMessage( $message );
    }

    public function getMessage(Message $message): ?object
    {
        return $this->messageRepository->find( $message );
    }

    public function getMessageById(int $messageId): ?object
    {
        return $this->messageRepository->find( $messageId );
    }

    public function getMessageByAuthor(User $user): ?object
    {
        return  $this->messageRepository->findOneBy( ['fromId'=> $user->getId()]);
    }

    public function getMessageByAuthorId(int $authorId): ?object
    {
        return $this->messageRepository->findOneBy(['fromId' => $authorId ]);
    }

    public function getMessageByRecipient(User $recipient): ?object
    {
        return $this->messageRepository->findOneBy(['forId' => $recipient->getId() ]);

    }

    public function getMessageByRecipientId(int $recipientId): object
    {
        return $this->messageRepository->findOneBy(['forId' => $recipientId ]);

    }

    public function getInboxMessage(User $user): array
    {
        return $this->messageRepository->findBy(['forId' => $user->getId() ]);
    }

    public function getOutboxMessage(User $user): array
    {
        return $this->messageRepository->findBy(['fromId' => $user->getId() ]);

    }
}