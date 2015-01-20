<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shoppingcarts
 *
 * @ORM\Table(name="ShoppingCarts", indexes={@ORM\Index(name="fk_ShoppingCarts_Users1_idx", columns={"Users_id"}), @ORM\Index(name="fk_ShoppingCarts_Items1_idx", columns={"Items_id"})})
 * @ORM\Entity(repositoryClass="KTU\ShopBundle\Entity\ShoppingcartsRepository")
 */
class Shoppingcarts
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
     *   @ORM\JoinColumn(name="Items_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $items;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Users_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $users;



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
     * @return Shoppingcarts
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
     * Set users
     *
     * @param \KTU\ShopBundle\Entity\Users $users
     * @return Shoppingcarts
     */
    public function setUsers(\KTU\ShopBundle\Entity\Users $users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return \KTU\ShopBundle\Entity\Users 
     */
    public function getUsers()
    {
        return $this->users;
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
     * @return Shoppingcarts
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }
}
