<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ad
 * @ORM\Table(name="ad")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdRepository")
 */
class Ad
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
     * @ORM\Column(name="name", type="string", length=70, unique=true,  nullable=false)
     * Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="price", type="float", nullable=false)
     * Assert\NotBlank()
     */
    private $price;

    /**
     * @var string
     * @ORM\Column(name="seller", type="string", nullable=false)
     * Assert\NotBlank()
     */
    private $seller;

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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * @param string $seller
     */
    public function setSeller($seller)
    {
        $this->seller = $seller;
    }
}

