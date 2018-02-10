<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 02.02.18
 * Time: 21:01
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NewsRepository")
 */
class News
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
     * @ORM\Column(name="name", type="string", length=100, unique=true)
     * Assert\NotNull(message="You must enter the name of news")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     * Assert\NotNull(message="You must enter text news")
     */
    private $description;

    /**
     * @var bool
     * @ORM\Column(name="isActive", type="boolean",nullable=true)
     */
    private $isActive;

    /**
     * @var bool
     * @ORM\Column(name="isAnalytic", type="boolean")
     */
    private $isAnalytic;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category", inversedBy="news")
     * @ORM\JoinColumn(name="news_category")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="news")
     * @ORM\JoinTable(name="news_tag")
     */
    private $tag;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Images", mappedBy="news", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="news")
     * @ORM\OrderBy({"plus" = "ASC", "createdAt" = "DESC"})
     */
    private $comments;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var integer
     * @ORM\Column(name="countRead", type="integer", nullable=true)
     */
    private $countRead;

    /**
     * @return mixed
     */

    public function getisAnalytic()
    {
        return $this->isAnalytic;
    }

    /**
     * @param mixed $isAnalytic
     */
    public function setIsAnalytic($isAnalytic)
    {
        $this->isAnalytic = $isAnalytic;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return ArrayCollection|Comment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getCountRead()
    {
        return $this->countRead;
    }

    /**
     * @param int $countRead
     */
    public function setCountRead($countRead)
    {
        $this->countRead = $countRead;
    }

    public function __construct() {
        $this->category = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

}