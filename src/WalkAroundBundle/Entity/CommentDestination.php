<?php

namespace WalkAroundBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CommentDestination
 *
 * @ORM\Table(name="comment_destination")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\CommentDestinationRepository")
 */
class CommentDestination
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
     * @ORM\ManyToOne(targetEntity="Destination", inversedBy="comments")
     * @ORM\JoinColumn(name="destination_id", referencedColumnName="id")
     */
    private $destination;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addedOn", type="datetime")
     */
    private $addedOn;

    /**
     * @var int
     *
     * @ORM\Column(name="addedBy", type="integer")
     */
    private $addedBy;

    /**
     * @var User
     * @ORM\ManyToOne( targetEntity="User", inversedBy="commentsDestination")
     * @ORM\JoinColumn( name="addedBy", referencedColumnName="id")
     */
    private $addedUser;

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
     * @var CommentDestination
     *
     * @ORM\ManyToOne( targetEntity="CommentDestination", inversedBy="comments")
     * @ORM\JoinColumn( name="idCommentRe", referencedColumnName="id")
     */
    private $comment;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany( targetEntity="CommentDestination", mappedBy="comment")
     */
    private $comments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CommentDestinationLiked", mappedBy="commentDestination")
     */
    private $likes;

    public function __construct( )
    {

        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();

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
     * @return CommentDestination
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
     * Set addedOn
     *
     * @param \DateTime $addedOn
     *
     * @return CommentDestination
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
     * Set addedBy
     *
     * @param integer $addedBy
     *
     * @return CommentDestination
     */
    public function setAddedBy($addedBy)
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    /**
     * Get addedBy
     *
     * @return int
     */
    public function getAddedBy()
    {
        return $this->addedBy;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return CommentDestination
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
     * @return CommentDestination
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
     * @return Destination
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param Destination $destination
     * @return CommentDestination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return User
     */
    public function getAddedUser()
    {
        return $this->addedUser;
    }

    /**
     * @param User $addedUser
     * @return CommentDestination
     */
    public function setAddedUser($addedUser)
    {
        $this->addedUser = $addedUser;
        return $this;
    }

    /**
     * @return CommentDestination
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param CommentDestination $comment
     * @return CommentDestination
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param CommentDestination $comments
     * @return CommentDestination
     */
    public function setComments($comments)
    {
        $this->comments[] = $comments;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     *
     * @param ArrayCollection $like
     * @return CommentDestination
     */
    public function setLike($like)
    {
        $this->likes[] = $like;
        return $this;
    }


}

