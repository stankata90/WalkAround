<?php

namespace WalkAroundBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * CommentDestinationLiked
 *
 * @ORM\Table(name="comment_destination_liked")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\CommentDestinationLikedRepository")
 */
class CommentDestinationLiked extends EntityRepository
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
     * @ORM\Column(name="comment_id", type="integer")
     */
    private $commentId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="addedOn", type="string", length=255)
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
     * Set commentId
     *
     * @param integer $commentId
     *
     * @return CommentDestinationLiked
     */
    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;

        return $this;
    }

    /**
     * Get commentId
     *
     * @return int
     */
    public function getCommentId()
    {
        return $this->commentId;
    }

    /**
     * @var CommentDestination
     *
     * @ORM\ManyToOne(targetEntity="CommentDestination", inversedBy="likes")
     * @ORM\JoinColumn( name="comment_id", referencedColumnName="id")
     */
    private $commentDestination;

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return CommentDestinationLiked
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="commentsDestinationLikes" )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $addUser;

    /**
     * Set addedOn
     *
     * @param string $addedOn
     *
     * @return CommentDestinationLiked
     */
    public function setAddedOn($addedOn)
    {
        $this->addedOn = $addedOn;

        return $this;
    }

    /**
     * Get addedOn
     *
     * @return string
     */
    public function getAddedOn()
    {
        return $this->addedOn;
    }

    /**
     * @return CommentDestination
     */
    public function getCommentDestination()
    {
        return $this->commentDestination;
    }

    /**
     * @param CommentDestination $commentDestination
     */
    public function setCommentDestination($commentDestination)
    {
        $this->commentDestination = $commentDestination;
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
     * @return CommentDestinationLiked
     */
    public function setAddUser($addUser)
    {
        $this->addUser = $addUser;
        return $this;
    }

}

