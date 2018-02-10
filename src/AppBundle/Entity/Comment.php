<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="parentId")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parentId;

    /**
     * @var
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * Many comments have One user.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comment")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(name="plus", type="integer")
     */
    private $plus;

    /**
     * @var int
     * @ORM\Column(name="minus", type="integer")
     */
    private $minus;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\News", inversedBy="comments")
     */
    private $news;

    /**
     * @var bool
     * @ORM\Column(name="isActive", type="boolean",nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="comment_opinion")
     * @ORM\JoinTable(name="user_comment_opinion")
     */
    private $user_opinion;

    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getUserOpinion()
    {
        return $this->user_opinion;
    }

    /**
     * @param mixed $user_opinion
     */
    public function setUserOpinion($user_opinion)
    {
        $this->user_opinion = $user_opinion;
    }

    /**
     * @return mixed
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @param mixed $news
     */
    public function setNews($news)
    {
        $this->news = $news;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return ArrayCollection|User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getPlus()
    {
        return $this->plus;
    }

    /**
     * @param int $plus
     */
    public function setPlus($plus)
    {
        $this->plus = $plus;
    }

    /**
     * @return int
     */
    public function getMinus()
    {
        return $this->minus;
    }

    /**
     * @param int $minus
     */
    public function setMinus($minus)
    {
        $this->minus = $minus;
    }

    public function __construct() {
        $this->user = new ArrayCollection();
        $this->user_opinion = new ArrayCollection();
    }

}

