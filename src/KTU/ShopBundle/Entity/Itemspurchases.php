<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Itemspurchases
 *
 * @ORM\Table(name="ItemsPurchases", indexes={@ORM\Index(name="fk_ItemsPurchases_Items1_idx", columns={"Items_id"}), @ORM\Index(name="fk_ItemsPurchases_Purchases1_idx", columns={"Purchases_id"})})
 * @ORM\Entity
 */
class Itemspurchases
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Items
     *
     * @ORM\ManyToOne(targetEntity="Items")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Items_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $items;

    /**
     * @var \Purchases
     *
     * @ORM\ManyToOne(targetEntity="Purchases")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Purchases_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $purchases;

    /**
     * @var float
     *
     * @ORM\Column(name="itemPrice", type="float", precision=10, scale=0, nullable=false)
     */
    private $itemprice;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * Get item price
     *
     * @return float
     */
    public function getItemprice()
    {
        return $this->itemprice;
    }

    /**
     * Set item price
     *
     * @param float $itemprice
     * @return Itemspurchases
     */
    public function setItemprice($itemprice)
    {
        $this->itemprice = $itemprice;

        return $this;
    }

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
     * Set items
     *
     * @param \KTU\ShopBundle\Entity\Items $items
     * @return Itemspurchases
     */
    public function setItems(\KTU\ShopBundle\Entity\Items $items = null)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get items
     *
     * @return \KTU\ShopBundle\Entity\Items 
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set purchases
     *
     * @param \KTU\ShopBundle\Entity\Purchases $purchases
     * @return Itemspurchases
     */
    public function setPurchases(\KTU\ShopBundle\Entity\Purchases $purchases = null)
    {
        $this->purchases = $purchases;

        return $this;
    }

    /**
     * Get purchases
     *
     * @return \KTU\ShopBundle\Entity\Purchases 
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     * @return Itemspurchases
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

}
