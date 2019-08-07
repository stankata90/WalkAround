<?php

namespace WalkAroundBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\MessageRepository")
 */
class Message
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
     * @ORM\Column(name="from_id", type="integer")
     */
    private $fromId;

    /**
     * @var int
     *
     * @ORM\Column(name="for_id", type="integer")
     */
    private $forId;

    /**
     * @var User
     *
     * @ORM\ManyToOne( targetEntity="User", inversedBy="sendMessages")
     * @ORM\JoinColumn(name="from_id", referencedColumnName="id")
     */
    private $fromUser;

    /**
     * @var User
     *
     * @ORM\ManyToOne( targetEntity="User", inversedBy="receivedMessages")
     * @ORM\JoinColumn(name="for_id", referencedColumnName="id")
     */
    private $forUser;

    /**
     * @var int
     *
     * @ORM\Column(name="seen", type="integer", nullable=true)
     */
    private $seen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="seenOn", type="datetime", nullable=true)
     */
    private $seenOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addedOn", type="datetime")
     */
    private $addedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="string", length=255)
     */
    private $about;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;



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
     * Set fromId
     *
     * @param integer $fromId
     *
     * @return Message
     */
    public function setFromId($fromId)
    {
        $this->fromId = $fromId;

        return $this;
    }

    /**
     * Get fromId
     *
     * @return int
     */
    public function getFromId()
    {
        return $this->fromId;
    }

    /**
     * Set forId
     *
     * @param integer $forId
     *
     * @return Message
     */
    public function setForId($forId)
    {
        $this->forId = $forId;

        return $this;
    }

    /**
     * Get forId
     *
     * @return int
     */
    public function getForId()
    {
        return $this->forId;
    }

    /**
     * Set seen
     *
     * @param integer $seen
     *
     * @return Message
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get seen
     *
     * @return int
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * @return User
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * @param User $fromUser
     * @return Message
     */
    public function setFromUser($fromUser)
    {
        $this->fromUser = $fromUser;
        return $this;
    }

    /**
     * @return User
     */
    public function getForUser()
    {
        return $this->forUser;
    }

    /**
     * @param User $forUser
     * @return Message
     */
    public function setForUser($forUser)
    {
        $this->forUser = $forUser;
        return $this;
    }



    /**
     * Set seenOn
     *
     * @param \DateTime $seenOn
     *
     * @return Message
     */
    public function setSeenOn($seenOn)
    {
        $this->seenOn = $seenOn;

        return $this;
    }

    /**
     * Get seenOn
     *
     * @return \DateTime
     */
    public function getSeenOn()
    {
        return $this->seenOn;
    }

    /**
     * Set addedOn
     *
     * @param \DateTime $addedOn
     *
     * @return Message
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
     * Set about
     *
     * @param string $about
     *
     * @return Message
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Message
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
}

