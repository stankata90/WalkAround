<?php

namespace WalkAroundBundle\Entity;

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
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var int
     *
     * @ORM\Column(name="idCommentRe", type="integer")
     */
    private $idCommentRe;


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
}

