<?php

namespace WalkAroundBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollectionAlias;
use Doctrine\ORM\Mapping as ORM;

/**
 * Friendship
 *
 * @ORM\Table(name="friendship")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\FriendshipRepository")
 */
class Friendship
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
     * @ORM\Column(name="sent_id", type="integer")
     */
    private $sentId;

    /**
     * @var int
     *
     * @ORM\Column(name="accepted_id", type="integer")
     */
    private $acceptedId;

    /**
     * @var User
     *
     * @ORM\ManyToOne( targetEntity="User")
     * @ORM\JoinColumn(name="sent_id", referencedColumnName="id")
     */
    private $sentUser;

    /**
     * @var User
     *accepted_id
     * @ORM\ManyToOne( targetEntity="User")
     * @ORM\JoinColumn(name="accepted_id", referencedColumnName="id")
     */
    private $acceptedUser;

    /**
     * @var ArrayCollectionAlias
     *
     * @ORM\OneToMany(targetEntity="Friend", mappedBy="friendship")
     */
    private $friends;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addedOn", type="datetime")
     */
    private $addedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="acceptedOn", type="datetime", nullable=true)
     */
    private $acceptedOn;

    public function __construct()
    {
        $this->friend = new ArrayCollectionAlias();
    }

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
     * Set sentId
     *
     * @param integer $sentId
     *
     * @return Friendship
     */
    public function setSentId($sentId)
    {
        $this->sentId = $sentId;

        return $this;
    }

    /**
     * Get sentId
     *
     * @return int
     */
    public function getSentId()
    {
        return $this->sentId;
    }

    /**
     * Set acceptedId
     *
     * @param integer $acceptedId
     *
     * @return Friendship
     */
    public function setAcceptedId($acceptedId)
    {
        $this->acceptedId = $acceptedId;

        return $this;
    }

    /**
     * Get acceptedId
     *
     * @return int
     */
    public function getAcceptedId()
    {
        return $this->acceptedId;
    }

    /**
     * Set addedOn
     *
     * @param \DateTime $addedOn
     *
     * @return Friendship
     */
    public function setAddedOn($addedOn)
    {
        $this->addedOn = $addedOn;

        return $this;
    }

    /**
     * Get addedOn
     *
     * @return \DateTime
     */
    public function getAddedOn()
    {
        return $this->addedOn;
    }

    /**
     * Set acceptedOn
     *
     * @param \DateTime $acceptedOn
     *
     * @return Friendship
     */
    public function setAcceptedOn($acceptedOn)
    {
        $this->acceptedOn = $acceptedOn;

        return $this;
    }

    /**
     * Get acceptedOn
     *
     * @return \DateTime
     */
    public function getAcceptedOn()
    {
        return $this->acceptedOn;
    }

    /**
     * @return User
     */
    public function getSentUser(): User
    {
        return $this->sentUser;
    }

    /**
     * @param User $sentUser
     * @return Friendship
     */
    public function setSentUser(User $sentUser)
    {
        $this->sentUser = $sentUser;
        return $this;
    }

    /**
     * @return User
     */
    public function getAcceptedUser(): User
    {
        return $this->acceptedUser;
    }

    /**
     * @param User $acceptedUser
     * @return Friendship
     */
    public function setAcceptedUser(User $acceptedUser)
    {
        $this->acceptedUser = $acceptedUser;
        return $this;
    }

    /**
     * @return ArrayCollectionAlias
     */
    public function getFriend()
    {
        return $this->friends;
    }

    /**
     * @param Friend $friend
     * @return Friendship
     */
    public function setFriend(Friend $friend)
    {
        $this->friends[] = $friend;
        return $this;
    }

}

