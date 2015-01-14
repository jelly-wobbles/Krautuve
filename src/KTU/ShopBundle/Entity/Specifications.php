<?php

namespace KTU\ShopBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Specifications
 *
 * @ORM\Table(name="Specifications", indexes={@ORM\Index(name="fk_Specifications_ItemsDetails1_idx", columns={"ItemsDetails_id"})})
 * @ORM\Entity
 */

class Specifications
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=100, nullable=false)
     */
    private $value;

    /**
     * @var \Itemsdetails
     *
     * @ORM\ManyToOne(targetEntity="Itemsdetails")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ItemsDetails_id", referencedColumnName="id", onDelete="cascade", nullable=false)
     * })
     */
    private $itemsdetails;

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
     * Get items details
     *
     * @return \KTU\ShopBundle\Entity\Itemsdetails
     */
    public function getItemsdetails()
    {
        return $this->itemsdetails;
    }

    /**
     * Set items details
     *
     * @param \KTU\ShopBundle\Entity\Itemsdetails $itemsdetails
     * @return Specifications
     */
    public function setItemsdetails($itemsdetails)
    {
        $this->itemsdetails = $itemsdetails;

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
     * Set name
     *
     * @param string $name
     * @return Specifications
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Specifications
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

} 