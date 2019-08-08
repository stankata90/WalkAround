<?php

namespace WalkAroundBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Service\Friend\FriendServiceInterface;
use WalkAroundBundle\Service\User\UserServiceInterface;

class FriendController extends Controller
{
    const SUCCESS_ADD = "Success added friend!";
    const SUCCESS_DEL = "Success removed friend!";
    const SUCCESS_ACCEPT = "Success accepted friend!";

    private $userService;
    private $friendService;

    public function __construct(
        UserServiceInterface  $userService,
        FriendServiceInterface $friendService
    )
    {
        $this->userService = $userService;
        $this->friendService = $friendService;
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("user/{id}/friend/invite", name="user_friend_invite", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @return RedirectResponse
     */
    public function sendInvite(int $id) {

        /** @var User $user */
        $user = $this->userService->findOneById(intval( $id ));
        if(!$user)
            return $this->goHome();

        if( $this->friendService->sendInvite( $user ) ) {
            $this->addFlash('info', self::SUCCESS_ADD);
        }

        return $this->redirectToRoute('friend_all');
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("user/{id}/friend/accept", name="user_friend_accept", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @return RedirectResponse
     */
    public function acceptInvite($id) {

        /** @var User $userEntity */
        $userEntity = $this->userService->findOneById(intval( $id ));
        if(!$userEntity)
            return $this->goHome();

        if( $this->friendService->acceptInvite( $userEntity ) ) {
                $this->addFlash('info', self::SUCCESS_ACCEPT);
            return $this->redirectToRoute('user_profile', ['id'=>$id] );
        }

        return $this->redirectToRoute('friend_all');
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("user/{id}/friend/delete", name="user_friend_delete_process", methods={"GET"}, requirements={"id"="\d+"})
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteProcess(int $id ) {
        /** @var User $friend */
        $friend = $this->userService->findOneById( intval( $id ) );
        if(!$friend)
            return $this->goHome();


        if($this->friendService->removeFriend( $friend ) ) {
            $this->addFlash('info', self::SUCCESS_DEL);
        }

        return $this->redirectToRoute('friend_all');
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Route("friend/all", name="friend_all", methods={"GET"})
     *
     * @return Response
     */
    public function allAction() {
        $friends = $this->friendService->findAll();
        return $this->render('friend/all.html.twig', ['friends' => $friends ]);
    }

    public function goHome() {
        return $this->redirectToRoute('friend_all');
    }
}
