<?php

namespace WalkAroundBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="\WalkAroundBundle\Repository\UserRepository")
 */
class User extends EntityRepository
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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="fullName", type="string", length=255)
     */
    private $fullName;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer", nullable=true)
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="sex", type="string", length=255, nullable=true)
     */
    private $sex;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="addedOn", type="datetime")
     */
    private $addedOn;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Destination", mappedBy="addedUser" )
     *
     */
    private $addDestinations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Destination", mappedBy="approvedUser" )
     *
     */
    private $approvedDestinations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CommentDestinationLiked", mappedBy="addUser")
     */
    private $commentsDestinationLikes;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="DestinationLiked", mappedBy="likedUser")
     */
    private $likesDestinations;


    public function __construct( EntityManagerInterface $em, ORM\ClassMetadata $class = null )
    {
        /** @var EntityManager $em */
        parent::__construct($em, $class == null ? new ORM\ClassMetadata( User::class ) : $class );

        $this->addDestinations = new ArrayCollection();
        $this->approvedDestinations = new ArrayCollection();
        $this->commentsDestinationLikes = new ArrayCollection();
        $this->likesDestinations = new ArrayCollection();
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
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set sex
     *
     * @param string $sex
     *
     * @return User
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return User
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set addedOn
     *
     * @param \DateTime $addedOn
     *
     * @return User
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
     * @return ArrayCollection
     */
    public function getAddDestinations()
    {
        return $this->addDestinations;
    }

    /**
     * @param ArrayCollection $destination
     * @return User
     */
    public function setAddDestinations($destination)
    {
        $this->addDestinations[] = $destination;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getApprovedDestinations()
    {
        return $this->approvedDestinations;
    }

    /**
     * @param ArrayCollection $approvedDestination
     * @return User
     */
    public function setApprovedDestinations($approvedDestination)
    {
        $this->approvedDestinations[] = $approvedDestination;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCommentsDestinationLikes()
    {
        return $this->commentsDestinationLikes;
    }

    /**
     * @param ArrayCollection $commentDestinationLike
     * @return User
     */
    public function setCommentDestinationLike($commentDestinationLike)
    {
        $this->commentsDestinationLikes[] = $commentDestinationLike;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLikesDestinations()
    {
        return $this->likesDestinations;
    }

    /**
     * @param ArrayCollection $likesDestination
     * @return User
     */
    public function setLikeDestination($likesDestination)
    {
        $this->likesDestinations[] = $likesDestination;
        return $this;
    }



}

