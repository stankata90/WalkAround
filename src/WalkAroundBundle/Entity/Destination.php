<?php

namespace WalkAroundBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Destination
 *
 * @ORM\Table(name="destination")
 * @ORM\Entity(repositoryClass="WalkAroundBundle\Repository\DestinationRepository")
 */
class Destination extends EntityRepository
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="free", type="string", length=255)
     */
    private $free;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="countSeen", type="integer")
     */
    private $countSeen;

    /**
     * @var int
     *
     * @ORM\Column(name="countVisited", type="integer")
     */
    private $countVisited;

    /**
     * @var int
     *
     * @ORM\Column(name="countLiked", type="integer")
     */
    private $countLiked;

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
     *
     * @ORM\ManyToOne(targetEntity="WalkAroundBundle\Entity\User", inversedBy="addDestinations")
     * @ORM\JoinColumn(name="addedBy", referencedColumnName="id")
     */
    private $addedUser;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approvedOn", type="datetime")
     */
    private $approvedOn;

    /**
     * @var int
     *
     * @ORM\Column(name="approvedBy", type="integer")
     */
    private $approvedBy;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="approvedDestinations")
     * @ORM\JoinColumn(name="approvedBy", referencedColumnName="id")
     */
    private $approvedUser;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="region_id", type="integer")
     */
    private $regionId;

    /**
     * @var Region
     *
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="destinations")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CommentDestination", mappedBy="destination")
     *
     */
    private $commentsUsers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany( targetEntity="DestinationLiked", mappedBy="likedDestination")
     */
    private $likesUsers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany( targetEntity="Event", mappedBy="destination")
     */
    private $eventsDestinations;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="City", inversedBy="destinations")
     * @ORM\JoinTable(name="destination_city")
     *
     */
    private $citiesTags;

    public function __construct( EntityManagerInterface $em, ORM\ClassMetadata $class = null )
    {
        /** @var EntityManager $em */
        parent::__construct($em, $class == null ? new ORM\ClassMetadata( User::class ) : $class );

        $this->commentsUsers = new ArrayCollection();
        $this->likesUsers = new ArrayCollection();
        $this->eventsDestinations = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Destination
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set free
     *
     * @param string $free
     *
     * @return Destination
     */
    public function setFree($free)
    {
        $this->free = $free;

        return $this;
    }

    /**
     * Get free
     *
     * @return string
     */
    public function getFree()
    {
        return $this->free;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Destination
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set countSeen
     *
     * @param integer $countSeen
     *
     * @return Destination
     */
    public function setCountSeen($countSeen)
    {
        $this->countSeen = $countSeen;

        return $this;
    }

    /**
     * Get countSeen
     *
     * @return int
     */
    public function getCountSeen()
    {
        return $this->countSeen;
    }

    /**
     * Set countVisited
     *
     * @param integer $countVisited
     *
     * @return Destination
     */
    public function setCountVisited($countVisited)
    {
        $this->countVisited = $countVisited;

        return $this;
    }

    /**
     * Get countVisited
     *
     * @return int
     */
    public function getCountVisited()
    {
        return $this->countVisited;
    }

    /**
     * Set countLiked
     *
     * @param integer $countLiked
     *
     * @return Destination
     */
    public function setCountLiked($countLiked)
    {
        $this->countLiked = $countLiked;

        return $this;
    }

    /**
     * Get countLiked
     *
     * @return int
     */
    public function getCountLiked()
    {
        return $this->countLiked;
    }

    /**
     * Set addedOn
     *
     * @param \DateTime $addedOn
     *
     * @return Destination
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
     * @return Destination
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
     * Set approvedOn
     *
     * @param \DateTime $approvedOn
     *
     * @return Destination
     */
    public function setApprovedOn($approvedOn)
    {
        $this->approvedOn = $approvedOn;

        return $this;
    }

    /**
     * Get approvedOn
     *
     * @return \DateTime
     */
    public function getApprovedOn()
    {
        return $this->approvedOn;
    }

    /**
     * Set approvedBy
     *
     * @param integer $approvedBy
     *
     * @return Destination
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * Get approvedBy
     *
     * @return int
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Destination
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
     * Set region
     *
     * @param integer $regionId
     *
     * @return Destination
     */
    public function setRegionId($regionId )
    {
        $this->regionId = $regionId;

        return $this;
    }

    /**
     * Get region
     *
     * @return int
     */
    public function getRegionId()
    {
        return $this->regionId;
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
     */
    public function setAddedUser($addedUser)
    {
        $this->addedUser = $addedUser;
    }

    /**
     * @return User
     */
    public function getApprovedUser()
    {
        return $this->approvedUser;
    }

    /**
     * @param User $approvedUser
     */
    public function setApprovedUser($approvedUser)
    {
        $this->approvedUser = $approvedUser;
    }

    /**
     * @return ArrayCollection
     */
    public function getCommentsUsers()
    {
        return $this->commentsUsers;
    }

    /**
     * @param ArrayCollection $comment
     * @return Destination
     */
    public function setCommentsUsers($comment)
    {
        $this->commentsUsers[] = $comment;
        return $this;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param Region $region
     * @return Destination
     */
    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLikesUsers()
    {
        return $this->likesUsers;
    }

    /**
     * @param ArrayCollection $likeUser
     * @return Destination
     */
    public function setLikeUser($likeUser)
    {
        $this->likesUsers[] = $likeUser;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEventsDestinations()
    {
        return $this->eventsDestinations;
    }

    /**
     * @param ArrayCollection $eventDestination
     * @return Destination
     */
    public function setEventDestination($eventDestination)
    {
        $this->eventsDestinations[] = $eventDestination;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCitiesTags()
    {
        return $this->citiesTags;
    }

    /**
     * @param ArrayCollection $cityTag
     * @return Destination
     */
    public function setCityTag($cityTag)
    {
        $this->citiesTags[] = $cityTag;
        return $this;
    }




}

