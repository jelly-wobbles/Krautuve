<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Purchases
 *
 * @ORM\Table(name="Purchases", indexes={@ORM\Index(name="fk_Purchases_Users1_idx", columns={"Users_id"}), @ORM\Index(name="fk_Purchases_PurchaseStatuses1_idx", columns={"PurchaseStatuses_id"})})
 * @ORM\Entity(repositoryClass="KTU\ShopBundle\Entity\PurchasesRepository")
 */
class Purchases
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var \Purchasestatuses
     *
     * @ORM\ManyToOne(targetEntity="Purchasestatuses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PurchaseStatuses_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $purchasestatuses;

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
     * Set date
     *
     * @param \DateTime $date
     * @return Purchases
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set purchasestatuses
     *
     * @param \KTU\ShopBundle\Entity\Purchasestatuses $purchasestatuses
     * @return Purchases
     */
    public function setPurchasestatuses(\KTU\ShopBundle\Entity\Purchasestatuses $purchasestatuses = null)
    {
        $this->purchasestatuses = $purchasestatuses;

        return $this;
    }

    /**
     * Get purchasestatuses
     *
     * @return \KTU\ShopBundle\Entity\Purchasestatuses 
     */
    public function getPurchasestatuses()
    {
        return $this->purchasestatuses;
    }

    /**
     * Set users
     *
     * @param \KTU\ShopBundle\Entity\Users $users
     * @return Purchases
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
}
