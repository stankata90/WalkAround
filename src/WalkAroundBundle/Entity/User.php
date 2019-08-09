<?php

namespace WalkAroundBundle\Entity;

//use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="\WalkAroundBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
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
     * @ORM\OneToMany(targetEntity="CommentDestination", mappedBy="addedUser" )
     *
     */
    private $commentsDestination;

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

    /**
     * @var ArrayCollection
     * @ORM\OneToMany( targetEntity="Event", mappedBy="addUser")
     */
    private $createdEventsDestinations;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany( targetEntity="EventUser", mappedBy="user")
     */
    private $eventsDestinations;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany( targetEntity="Message", mappedBy="fromUser")
     */
    private $sendMessages;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany( targetEntity="Message", mappedBy="forUser")
     */
    private $receivedMessages;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_role",
     *          joinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id") },
     *          inverseJoinColumns={ @ORM\JoinColumn(name="role_id", referencedColumnName="id") }
     *      )
     */
    private $roles;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Friend", mappedBy="friendUser")
     */
    private $friends;

    public function __construct( )
    {

        $this->addDestinations = new ArrayCollection();
        $this->approvedDestinations = new ArrayCollection();
        $this->commentsDestinationLikes = new ArrayCollection();
        $this->likesDestinations = new ArrayCollection();
        $this->createdEventsDestinations = new ArrayCollection();
        $this->eventsDestinations = new ArrayCollection();
        $this->sendMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->friends = new ArrayCollection();
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
     * @throws \Exception
     * @return User
     */
    public function setAge($age)
    {

        if( !is_integer( intval($age )) ) {
            throw new Exception( 'eror age max ');
        }
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
    public function getCommentsDestination()
    {
        return $this->commentsDestination;
    }

    /**
     * @param ArrayCollection $commentDestination
     * @return User
     */
    public function setCommentDestination($commentDestination)
    {
        $this->commentsDestination[] = $commentDestination;
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

    /**
     * @return ArrayCollection
     */
    public function getCreatedEventsDestinations()
    {
        return $this->createdEventsDestinations;
    }

    /**
     * @param ArrayCollection $createEventDestination
     * @return User
     */
    public function setCreateEventDestination($createEventDestination)
    {
        $this->createdEventsDestinations[] = $createEventDestination;
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
     *
     * @param ArrayCollection $eventDestination
     * @return User
     */
    public function setEntDestination($eventDestination)
    {
        $this->eventsDestinations[] = $eventDestination;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSendMessages()
    {
        return $this->sendMessages;
    }

    /**
     * @return User
     * @param ArrayCollection $sendMessage
     */
    public function setSendMessages($sendMessage)
    {
        $this->sendMessages[] = $sendMessage;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getReceivedMessages()
    {
        return $this->receivedMessages;
    }

    /**
     * @param ArrayCollection $receivedMessage
     * @return User
     */
    public function setReceivedMessage($receivedMessage)
    {
        $this->receivedMessages[] = $receivedMessage;
        return $this;
    }

    /**
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $stringRoles = [];

        foreach ( $this->roles as $role ) {
            /** @var Role $role */
            $stringRoles[] =  $role->getRole();
        }
        return $stringRoles;
    }


    /**
     * @param Role $role
     * @return User
     */
    public function addRole( Role $role )
    {
        $this->roles[] = $role;

        return $this;
    }


    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return false;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getFullName();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        return false;
    }

    /**
     * @return ArrayCollection
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @param Friend $friend
     * @return User
     */
    public function setFriend(Friend $friend)
    {
        $this->friends = $friend;
        return $this;
    }


}

