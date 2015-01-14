<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Images
 *
 * @ORM\Table(name="Images", indexes={@ORM\Index(name="fk_Images_ItemsDetails1_idx", columns={"ItemsDetails_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Images
{

    const UPLOAD_ROOT_DIR = "uploads/items";

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
     * @ORM\Column(name="path", type="string", length=255, nullable=false)
     */
    private $path;

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
     * @var
     */
    private $temp;

    /**
     * @var File
     *
     */
    protected $file;

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
     * Set path
     *
     * @param string $path
     * @return Images
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set itemsdetails
     *
     * @param \KTU\ShopBundle\Entity\Itemsdetails $itemsdetails
     * @return Images
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
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move(self::UPLOAD_ROOT_DIR, $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->path);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = self::UPLOAD_ROOT_DIR.'/'.$this->path;
        if ($file) {
            unlink($file);
        }
    }
}
