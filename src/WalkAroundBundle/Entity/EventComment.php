<?php

namespace WalkAroundBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * EventComment
 *
 * @ORM\Table(name="event_comment")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\EventCommentRepository")
 */
class EventComment
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
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="eventComments")
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $addUser;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var int
     *
     * @ORM\Column(name="idCommentRe", type="integer", nullable=true)
     */
    private $idCommentRe;

    /**
     * @var EventComment;
     *
     * @ORM\ManyToOne(targetEntity="EventComment", inversedBy="commentsRe")
     * @ORM\JoinColumn(name="idCommentRe", referencedColumnName="id")
     */
    private $comment;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventComment", mappedBy="comment")
     */
    private $commentsRe;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addedOn", type="datetime")
     */
    private $addedOn;

    public function __construct( )
    {

        $this->commentsRe = new ArrayCollection();
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
     * Set eventId
     *
     * @param integer $eventId
     *
     * @return EventComment
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
     * @param Event $event
     * @return EventComment
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
     * @return EventComment
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
     * @return EventComment
     */
    public function setAddUser($addUser)
    {
        $this->addUser = $addUser;
        return $this;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return EventComment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set idCommentRe
     *
     * @param integer $idCommentRe
     *
     * @return EventComment
     */
    public function setIdCommentRe($idCommentRe)
    {
        $this->idCommentRe = $idCommentRe;

        return $this;
    }

    /**
     * Get idCommentRe
     *
     * @return int
     */
    public function getIdCommentRe()
    {
        return $this->idCommentRe;
    }

    /**
     * Set addedOn
     *
     * @param \DateTime $addedOn
     *
     * @return EventComment
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
     * @return EventComment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param EventComment $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return ArrayCollection
     */
    public function getCommentsRe()
    {
        return $this->commentsRe;
    }

    /**
     * @param EventComment $reComment
     * @return EventComment
     */
    public function setCommentsRe($reComment)
    {
        $this->commentsRe[] = $reComment;
        return $this;
    }

}

