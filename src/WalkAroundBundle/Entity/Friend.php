<?php

namespace WalkAroundBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Friend
 *
 * @ORM\Table(name="friend")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\FriendRepository")
 */
class Friend
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="my_id", type="integer")
     */
    private $myId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="WalkAroundBundle\Entity\User")
     * @ORM\JoinColumn(name="my_id", referencedColumnName="id")
     */
    private $myUser;

    /**
     * @var int
     *
     * @ORM\Column(name="friend_id", type="integer")
     */
    private $friendId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="WalkAroundBundle\Entity\User", inversedBy="friends")
     * @ORM\JoinColumn(name="friend_id", referencedColumnName="id")
     */
    private $friendUser;

    /**
     * @var int
     *
     * @ORM\Column(name="friendship_id", type="integer")
     */
    private $friendshipId;

    /**
     * @var Friendship
     *
     * @ORM\ManyToOne(targetEntity="Friendship", inversedBy="friends")
     * @ORM\JoinColumn(name="friendship_id", referencedColumnName="id")
     */
    private $friendship;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set myId
     *
     * @param integer $myId
     *
     * @return Friend
     */
    public function setMyId($myId)
    {
        $this->myId = $myId;

        return $this;
    }

    /**
     * Get myId
     *
     * @return int
     */
    public function getMyId()
    {
        return $this->myId;
    }

    /**
     * Set friendId
     *
     * @param integer $friendId
     *
     * @return Friend
     */
    public function setFriendId($friendId)
    {
        $this->friendId = $friendId;

        return $this;
    }

    /**
     * Get friendId
     *
     * @return int
     */
    public function getFriendId()
    {
        return $this->friendId;
    }

    /**
     * Set friendshipId
     *
     * @param integer $friendshipId
     *
     * @return Friend
     */
    public function setFriendshipId($friendshipId)
    {
        $this->friendshipId = $friendshipId;

        return $this;
    }

    /**
     * Get friendshipId
     *
     * @return int
     */
    public function getFriendshipId()
    {
        return $this->friendshipId;
    }

    /**
     * @return User
     */
    public function getMyUser(): User
    {
        return $this->myUser;
    }

    /**
     * @param User $myUser
     * @return Friend
     */
    public function setMyUser(User $myUser)
    {
        $this->myUser = $myUser;
        return $this;
    }

    /**
     * @return User
     */
    public function getFriendUser(): User
    {
        return $this->friendUser;
    }

    /**
     * @param User $friendUser
     * @return Friend
     */
    public function setFriendUser(User $friendUser)
    {
        $this->friendUser = $friendUser;
        return $this;
    }

    /**
     * @return Friendship
     */
    public function getFriendship(): Friendship
    {
        return $this->friendship;
    }

    /**
     * @param Friendship $friendship
     * @return Friend
     */
    public function setFriendship(Friendship $friendship)
    {
        $this->friendship = $friendship;
        return $this;
    }


}

