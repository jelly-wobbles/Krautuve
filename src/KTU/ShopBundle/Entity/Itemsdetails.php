<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Itemsdetails
 *
 * @ORM\Table(name="ItemsDetails", indexes={@ORM\Index(name="IDX_2EB8A3116C5F42", columns={"Categories_id"})})
 * @ORM\Entity(repositoryClass="KTU\ShopBundle\Entity\ItemsdetailsRepository")
 */
class Itemsdetails
{

    const LITASMULTIPLIER = 3.4528;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \Categories
     *
     * @ORM\ManyToOne(targetEntity="Categories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Categories_id", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $categories;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", precision=10, scale=0)
     */
    private $discount;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="isRemoved", type="boolean")
     */
    private $isRemoved;

    /**
     * @var $items
     *
     * @ORM\OneToMany(targetEntity="Items", mappedBy="itemsdetails")
     */
    private $items;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Itemsdetails
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
     * Set description
     *
     * @param string $description
     * @return Itemsdetails
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
     * Set categories
     *
     * @param \KTU\ShopBundle\Entity\Categories $categories
     * @return Itemsdetails
     */
    public function setCategories(\KTU\ShopBundle\Entity\Categories $categories = null)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return \KTU\ShopBundle\Entity\Categories 
     */
    public function getCategories()
    {
        return $this->categories;
    }


    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return float
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        if($this->discount != 0){
            return round($this->price * ( 1 - $this->discount ), 2);
        }

        return round($this->price, 2);
    }


    /**
     * Get pure price
     *
     * @return float
     */
    public function getPurePrice()
    {
        return $this->price;
    }


    /**
     * Get price in Litas
     *
     * @return float
     */
    public function getPriceLT()
    {
        return $this->getPrice() * self::LITASMULTIPLIER;
    }

    /**
     * Get pure price in Litas
     *
     * @return float
     */
    public function getPurePriceLT()
    {
        return $this->price * self::LITASMULTIPLIER;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Itemsdetails
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get isRemoved
     *
     * @return int
     */
    public function getIsRemoved()
    {
        return $this->isRemoved;
    }

    /**
     * Set isRemoved
     *
     * @param int $isRemoved
     * @return Itemsdetails
     */
    public function setIsRemoved($isRemoved)
    {
        $this->isRemoved = $isRemoved;

        return $this;
    }

}
