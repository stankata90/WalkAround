<?php

namespace WalkAroundBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Friend
 *
 * @ORM\Table(name="friend")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\FriendRepository")
 */
class Friend extends EntityRepository
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
     * @ORM\ManyToOne( targetEntity="User", inversedBy="friends")
     * @ORM\JoinColumn(name="sent_id", referencedColumnName="id")
     */
    private $sentUser;

    /**
     * @var User
     *
     * @ORM\ManyToOne( targetEntity="User", inversedBy="friends")
     * @ORM\JoinColumn(name="accepted_id", referencedColumnName="id")
     */
    private $acceptedUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addedOn", type="datetime")
     */
    private $addedOn;

    public function __construct( EntityManagerInterface $em, ORM\ClassMetadata $class = null )
    {
        /** @var EntityManager $em */
        parent::__construct($em, $class == null ? new ORM\ClassMetadata( User::class ) : $class );
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
     * @return Friend
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
     * @return Friend
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
     * @return User
     */
    public function getSentUser()
    {
        return $this->sentUser;
    }

    /**
     * @param User $sentUser
     * @return Friend
     */
    public function setSentUser($sentUser)
    {
        $this->sentUser = $sentUser;
        return $this;
    }

    /**
     * @return User
     */
    public function getAcceptedUser()
    {
        return $this->acceptedUser;
    }

    /**
     * @param User $acceptedUser
     * @return Friend
     */
    public function setAcceptedUser($acceptedUser)
    {
        $this->acceptedUser = $acceptedUser;
        return $this;
    }

    /**
     * Set addedOn
     *
     * @param \DateTime $addedOn
     *
     * @return Friend
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
}

