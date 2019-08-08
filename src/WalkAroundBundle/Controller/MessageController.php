<?php

namespace WalkAroundBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Entity\Message;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Form\Message\MessageNewType;
use WalkAroundBundle\Service\Message\MessageServiceInterface;
use WalkAroundBundle\Service\User\UserServiceInterface;

class MessageController extends Controller
{
    const SUCCESS_SEND = "Success send mail!";
    const SUCCESS_DEL = "Success deleted mail!";

    /** @var User */
    private $currentUser;
    private $messageService;
    private $userService;

    public function __construct( MessageServiceInterface $messageService, UserServiceInterface $userService)
    {

        $this->messageService = $messageService;
        $this->userService = $userService;
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("/mailbox/inbox", name="mailbox_inbox", methods={"GET"})
     */
    public function inboxAction()
    {
        $this->currentUser = $this->getUser();
        /** @var Message $arrMessageEntity */
        $arrMessageEntity = $this->messageService->getInboxMessage( $this->currentUser );

        return $this->render("mailbox/inbox.html.twig", ['messages' => $arrMessageEntity]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("/mailbox/outbox", name="mailbox_outbox", methods={"GET"})
     */
    public function outboxAction()
    {
        $this->currentUser = $this->getUser();
        /** @var Message $arrMessageEntity */
        $arrMessageEntity = $this->messageService->getOutboxMessage( $this->currentUser );

        return $this->render("mailbox/outbox.html.twig", ['messages' => $arrMessageEntity]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("/mailbox/new/{id}", name="mailbox_new", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function newAction(int $id)
    {
        /** @var User $user */
        $user = $this->userService->findOneById( $id );
        if( !$user )
            return $this->redirectToRoute('mailbox_inbox');

        return $this->render("mailbox/new.html.twig", ['user' => $user, 'form' => $this->createForm( MessageNewType::class)->createView() ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("/mailbox/new/{id}", name="mailbox_new_process", methods={"POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function newProcess( int $id, Request $request  )
    {
        /** @var User $user */
        $user = $this->userService->findOneById( $id );
        if( !$user )
            return $this->redirectToRoute('mailbox_inbox');
        /** @var Message $message */
        $message = new Message();

        if( $this->messageService->createMessage($this->createForm(MessageNewType::class, $message ), $message->setForId($id), $request  )) {
                $this->addFlash( 'info', self::SUCCESS_SEND);
            return $this->redirectToRoute( 'user_profile', ['id'=> $id] );
        }

        return $this->redirectToRoute( 'mailbox_new', ['id' => $id ] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("/mailbox/{id}/view", name="mailbox_view", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function viewAction( int $id )
    {
        /** @var Message $mail */
        $mail = $this->messageService->getMessageById( $id );

        return $this->render("mailbox/view.html.twig", ['message' => $this->messageService->readMessage( $mail )] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("/mailbox/{id}/delete", name="mailbox_delete_process", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function deleteProcess(int $id)
    {
        /** @var Message $messageEntity */
        $messageEntity = $this->messageService->getMessageById( $id );
        $this->currentUser = $this->getUser();
        if( $messageEntity == null or $messageEntity->getForId() !== $this->currentUser->getId() ) {
            return $this->redirectToRoute('mailbox_inbox');
        }
        $this->messageService->removeMessage( $messageEntity );
        $this->addFlash( 'info', self::SUCCESS_DEL);
        return $this->redirectToRoute('mailbox_inbox');
    }
}
