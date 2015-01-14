<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ratings
 *
 * @ORM\Table(name="Ratings", indexes={@ORM\Index(name="fk_Ratings_Users1_idx", columns={"Users_id"}), @ORM\Index(name="fk_Ratings_ItemsDetails1_idx", columns={"ItemsDetails_id"})})
 * @ORM\Entity
 */
class Ratings
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
     * @ORM\Column(name="rating", type="integer", nullable=false)
     */
    private $rating;

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
     * @var \Itemsdetails
     *
     * @ORM\ManyToOne(targetEntity="Itemsdetails")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ItemsDetails_id", referencedColumnName="id", nullable=false, onDelete="cascade")
     * })
     */
    private $itemsdetails;



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
     * Set rating
     *
     * @param integer $rating
     * @return Ratings
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set users
     *
     * @param \KTU\ShopBundle\Entity\Users $users
     * @return Ratings
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
     * Set itemsdetails
     *
     * @param \KTU\ShopBundle\Entity\Itemsdetails $itemsdetails
     * @return Ratings
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
}
