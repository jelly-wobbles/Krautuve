<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Items
 *
 * @ORM\Table(name="Items", indexes={@ORM\Index(name="IDX_20DFC649C2FAA8C4", columns={"ItemStatuses_id"}), @ORM\Index(name="fk_Items_ItemsDetails1_idx", columns={"ItemsDetails_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="KTU\ShopBundle\Entity\ItemsRepository")
 */
class Items
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
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var \Itemstatuses
     *
     * @ORM\ManyToOne(targetEntity="Itemstatuses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ItemStatuses_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $itemstatuses;

    /**
     * @var \Itemsdetails
     *
     * @ORM\ManyToOne(targetEntity="Itemsdetails")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ItemsDetails_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     */
    private $itemsdetails;

    /**
     * @var $itemspurchases
     *
     * @ORM\OneToMany(targetEntity="Itemspurchases", mappedBy="items")
     */
    private $itemspurchases;

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
     * Set itemstatuses
     *
     * @param \KTU\ShopBundle\Entity\Itemstatuses $itemstatuses
     * @return Items
     */
    public function setItemstatuses(\KTU\ShopBundle\Entity\Itemstatuses $itemstatuses = null)
    {
        $this->itemstatuses = $itemstatuses;

        return $this;
    }

    /**
     * Get itemstatuses
     *
     * @return \KTU\ShopBundle\Entity\Itemstatuses 
     */
    public function getItemstatuses()
    {
        return $this->itemstatuses;
    }

    /**
     * Set itemsdetails
     *
     * @param \KTU\ShopBundle\Entity\Itemsdetails $itemsdetails
     * @return Items
     */
    public function setItemsdetails(\KTU\ShopBundle\Entity\Itemsdetails $itemsdetails = null)
    {
        $this->itemsdetails = $itemsdetails;

        return $this;
    }

    /**
     * Get itemsdetails
     *
     * @return \KTU\ShopBundle\Entity\Itemsdetails 
     */
    public function getItemsdetails()
    {
        return $this->itemsdetails;
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
     * @return Items
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }



}
