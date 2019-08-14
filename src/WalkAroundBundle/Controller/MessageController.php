<?php

namespace WalkAroundBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Entity\Mail;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Form\Message\MessageNewType;
use WalkAroundBundle\Service\Message\MailServiceInterface;
use WalkAroundBundle\Service\User\UserServiceInterface;

class MessageController extends Controller
{
    const SUCCESS_SEND = "Success send mail!";
    const SUCCESS_DEL = "Success deleted mail!";

    /** @var User */
    private $currentUser;
    private $messageService;
    private $userService;

    public function __construct(MailServiceInterface $messageService, UserServiceInterface $userService)
    {

        $this->messageService = $messageService;
        $this->userService = $userService;
    }

    public function createForm($type, $data = null, array $options = [])
    {
        return parent::createForm( $type, $data, $options);
    }

    public function getParameter($name)
    {
        return parent::getParameter($name);
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("/mailbox/inbox", name="mailbox_inbox", methods={"GET"})
     */
    public function inboxAction()
    {
        $this->currentUser = $this->getUser();
        /** @var Mail $arrMessageEntity */
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
        /** @var Mail $arrMessageEntity */
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
    public function newAction( $id )
    {
        /** @var User $user */
        $user = $this->userService->findOneById( intval($id) );
        if( !$user )
            return $this->goHome();

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
    public function newProcess( $id, Request $request  )
    {
        /** @var User $user */
        $user = $this->userService->findOneById( intval( $id ) );
        if( !$user )
            return $this->goHome();

        /** @var Mail $message */

        try {
            $this->messageService->createMessage( $this, $request, $user  );

            $this->addFlash( 'info', self::SUCCESS_SEND);
            return $this->redirectToRoute( 'user_profile', ['id'=> $id] );

        } catch  ( Exception $e ) {

            $this->addFlash('error', $e->getMessage() );

        }

        return $this->redirectToRoute( 'mailbox_new', ['id' => $id ] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("/mailbox/{id}/view", name="mailbox_view", methods={"GET"}, requirements={"id"="\d+"})
     * @param $id
     * @return Response
     */
    public function viewAction( $id )
    {
        /** @var Mail $mail */
        $mail = $this->messageService->getMessageById( intval( $id ) );
        if( !$mail )
            return $this->goHome();

        return $this->render("mailbox/view.html.twig", ['message' => $this->messageService->readMessage( $mail )] );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("/mailbox/{id}/delete", name="mailbox_delete_process", methods={"GET"}, requirements={"id"="\d+"})
     * @param $id
     * @return RedirectResponse
     */
    public function deleteProcess($id )
    {
        /** @var Mail $messageEntity */
        $messageEntity = $this->messageService->getMessageById( intval( $id ) );
        if( !$messageEntity )
            return $this->goHome();

        $this->currentUser = $this->getUser();
        if( $messageEntity == null or $messageEntity->getForId() !== $this->currentUser->getId() ) {
            return $this->redirectToRoute('mailbox_inbox');
        }

        $this->messageService->removeMessage( $messageEntity );
        $this->addFlash( 'info', self::SUCCESS_DEL);
        return $this->redirectToRoute('mailbox_inbox');
    }

    public function goHome() {
        return $this->redirectToRoute('mailbox_inbox');
    }
}
