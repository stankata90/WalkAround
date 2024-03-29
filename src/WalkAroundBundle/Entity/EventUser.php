<?php

namespace WalkAroundBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventUser
 *
 * @ORM\Table(name="event_user")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\EventUserRepository")
 */
class EventUser
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
     * @ORM\Column(name="event_id", type="integer")
     */
    private $eventId;

    /**
     * @var Event
     *
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="eventUsers")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     *
     */
    private $event;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="accepted", type="datetime", length=255, nullable=true)
     */
    private $accepted;



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
     * Set eventId
     *
     * @param integer $eventId
     *
     * @return EventUser
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     *
     * @param Event $event
     * @return EventUser
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }



    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return EventUser
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return EventUser
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }



    /**
     * Set accepted
     *
     * @param \DateTime $date
     *
     * @return EventUser
     */
    public function setAccepted( $date )
    {
        $this->accepted = $date;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return \DateTime
     */
    public function getAccepted()
    {
        return $this->accepted;
    }
}

