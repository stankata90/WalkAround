<?php


namespace WalkAroundBundle\Service\Friend;


use DateTime;
use Symfony\Component\Security\Core\Security;
use WalkAroundBundle\Entity\Friend;
use WalkAroundBundle\Entity\Friendship;
use WalkAroundBundle\Entity\User;
use WalkAroundBundle\Repository\FriendRepository;
use WalkAroundBundle\Repository\FriendshipRepository;
use WalkAroundBundle\Repository\UserRepository;

class FriendService implements FriendServiceInterface
{
    private $userRepository;
    /** @var User */
    private $currentUser;
    private $friendRepository;
    private $friendshipRepository;
    private $security;

    public function __construct(
        UserRepository $userRepository,
        FriendRepository $friendRepository,
        FriendshipRepository $friendshipRepository,
        Security $security
    )
    {
        $this->userRepository = $userRepository;
        $this->friendRepository = $friendRepository;
        $this->friendshipRepository = $friendshipRepository;
        $this->security = $security;
    }

    /**
     * @return object|null|Friend[]
     */
    public function findAll()
    {
        $this->currentUser = $this->security->getUser();
        $result = array_filter( $this->friendRepository->findBy(['myId' => $this->currentUser->getId() ] ), function ( $fr ) {
            /** @var Friend $fr */
            return $fr->getFriendship()->getAcceptedOn() != null;
        }) ;

        return $result;
    }

    /**
     * @param User $friend
     * @return Friend|array
     */
    public function findFriend(User $friend)
    {
       $this->currentUser = $this->security->getUser();

        $query = $this->friendRepository->createQueryBuilder('friend')
            ->where('friend.myId = :myid')
            ->andWhere('friend.friendId = :fid')
            ->setParameter('myid', $this->currentUser->getId())
            ->setParameter('fid', $friend->getId())
            ->getQuery();

        $products = $query->getResult();

        return $products;
    }

    public function sendInvite( User $user ) {
        $this->currentUser = $this->security->getUser();
        if( $this->findFriend($user) or $user->getId() == $this->currentUser->getId() )
            return false;
        $this->currentUser = $this->security->getUser();
        $friendshipEntity = new Friendship();

        $friendshipEntity
            ->setSentUser( $this->currentUser )
            ->setAcceptedUser( $user )
            ->setAddedOn(new DateTime());

        $this->friendshipRepository->insert( $friendshipEntity );

        $friedEntity = new Friend();
        $friedEntity
            ->setMyUser($this->currentUser)
            ->setFriendUser( $user )
            ->setFriendship( $friendshipEntity);
        $this->friendRepository->insert( $friedEntity );
        $friedEntity = new Friend();
        $friedEntity
            ->setMyUser($user)
            ->setFriendUser( $this->currentUser )
            ->setFriendship( $friendshipEntity);
        $this->friendRepository->insert( $friedEntity );

        return true;
    }

    public function acceptInvite( User $user ) {
        /** @var Friend[] $friendEntity */
        $friendEntity = $this->findFriend( $user );
        if( !$friendEntity )
            return false;

        /** @var Friendship $friendshipEntity */
        $friendshipEntity = $this->friendshipRepository->find( $friendEntity[0]->getFriendshipId());
        $friendshipEntity
            ->setAcceptedOn( new DateTime('now' ) );

        $this->friendshipRepository->update($friendshipEntity);

        return true;
    }

    public function removeFriend(User $friend)
    {
        /** @var Friend[] $friendEntity */
        $friendEntity = $this->findFriend( $friend );
        if( !$friendEntity )
           return false;

        /** @var Friendship $friendshipEntity */
        $friendshipEntity =$this->friendshipRepository->find( $friendEntity[0]->getFriendshipId() );

        $query = $this->friendRepository->createQueryBuilder('friend')
            ->where('friend.friendshipId = :id')
            ->setParameter('id', $friendEntity[0]->getFriendshipId())
            ->getQuery();

        $friends = $query->getResult();

        foreach ( $friends as $friend ) {
            /** @var $friend Friend */
            $this->friendRepository->delete( $friend );
        }

        if( $this->friendshipRepository->delete( $friendshipEntity ))
        return true;

        return false;
    }
}