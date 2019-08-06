<?php

namespace WalkAroundBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * DestinationLiked
 *
 * @ORM\Table(name="destination_liked")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\DestinationLikedRepository")
 */
class DestinationLiked
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
     * @ORM\Column(name="destination_id", type="integer")
     */
    private $destinationId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addedOn", type="datetime")
     */
    private $addedOn;

    /**
     * @var Destination
     *
     * @ORM\ManyToOne(targetEntity="Destination", inversedBy="likesUsers")
     * @ORM\JoinColumn(name="destination_id", referencedColumnName="id")
     *
     */
    private $likedDestination;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="likesDestinations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $likedUser;


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
     * Set destinationId
     *
     * @param integer $destinationId
     *
     * @return DestinationLiked
     */
    public function setDestinationId($destinationId)
    {
        $this->destinationId = $destinationId;

        return $this;
    }

    /**
     * Get destinationId
     *
     * @return int
     */
    public function getDestinationId()
    {
        return $this->destinationId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return DestinationLiked
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set addedOn
     *
     * @param \DateTime $addedOn
     *
     * @return DestinationLiked
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
     * @return Destination
     */
    public function getLikedDestination()
    {
        return $this->likedDestination;
    }

    /**
     * @param Destination $likedDestination
     * @return DestinationLiked
     */
    public function setLikedDestination($likedDestination)
    {
        $this->likedDestination = $likedDestination;
        return $this;
    }

    /**
     * @return User
     */
    public function getLikedUser()
    {
        return $this->likedUser;
    }

    /**
     * @param User $likedUser
     * @return DestinationLiked
     */
    public function setLikedUser($likedUser)
    {
        $this->likedUser = $likedUser;
        return $this;
    }



}

