<?php

namespace WalkAroundBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\EventRepository")
 */
class Event extends EntityRepository
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
     * @var Destination
     *
     * @ORM\ManyToOne(targetEntity="Destination", inversedBy="eventsDestinations" )
     * @ORM\JoinColumn(name="destination_id", referencedColumnName="id")
     */
    private $destination;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var User
     *
     * @ORM\ManyToOne( targetEntity="User", inversedBy="createdEventsDestinations" )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $addUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addedOn", type="datetime")
     */
    private $addedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="eventOn", type="datetime")
     */
    private $eventOn;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany( targetEntity="EventComment", mappedBy="event")
     */
    private $eventComments;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventUser", mappedBy="event")
     */
    private $eventUsers;

    public function __construct( EntityManagerInterface $em, ORM\ClassMetadata $class = null )
    {
        /** @var EntityManager $em */
        parent::__construct($em, $class == null ? new ORM\ClassMetadata( User::class ) : $class );
        $this->eventComments = new ArrayCollection();
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
     * Set destinationId
     *
     * @param integer $destinationId
     *
     * @return Event
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
     * @return Destination
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param Destination $destination
     * @return Event
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Event
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
     * @return User
     */
    public function getAddUser()
    {
        return $this->addUser;
    }

    /**
     * @param User $addUser
     */
    public function setAddUser($addUser)
    {
        $this->addUser = $addUser;
    }


    /**
     * Set addedOn
     *
     * @param \DateTime $addedOn
     *
     * @return Event
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
     * Set eventOn
     *
     * @param \DateTime $eventOn
     *
     * @return Event
     */
    public function setEventOn($eventOn)
    {
        $this->eventOn = $eventOn;

        return $this;
    }

    /**
     * Get eventOn
     *
     * @return \DateTime
     */
    public function getEventOn()
    {
        return $this->eventOn;
    }

    /**
     * @return ArrayCollection
     */
    public function getEventComments()
    {
        return $this->eventComments;
    }

    /**
     * @param ArrayCollection $eventComment
     *
     * @return Event
     */
    public function setEventComment($eventComment)
    {
        $this->eventComments[] = $eventComment;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEventUsers()
    {
        return $this->eventUsers;
    }

    /**
     * @param ArrayCollection $eventUsers
     * @return Event
     */
    public function setEventUsers($eventUsers)
    {
        $this->eventUsers = $eventUsers;
        return $this;
    }



}

