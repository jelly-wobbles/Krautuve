<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;
use KTU\ShopBundle\Entity\Items;
use KTU\ShopBundle\Entity\Itemsdetails;
use KTU\ShopBundle\Entity\Specifications;

class ItemsEditor
{
    const MAX_STORED = 100000;

    private $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function addItem($data, $images)
    {
        if($data != null){
            $itemDetails = new Itemsdetails();
            $itemDetails->setName($data['name']);
            $itemDetails->setDescription($data['description']);
            $itemDetails->setCategories($data['category']);
            $itemDetails->setPrice($data['price']);
            $itemDetails->setDiscount(0);
            $itemDetails->setIsRemoved(0);
            $this->em->persist($itemDetails);
            $this->em->flush();

            $specsCount = count($data['specs_names']);

            if($specsCount > 0){
                for($i = 0; $i < $specsCount; $i++)
                {
                    $specifications = new Specifications();
                    $specifications->setItemsdetails($itemDetails);
                    $specifications->setName($data['specs_names'][$i]);
                    $specifications->setValue($data['specs_values'][$i]);
                    $this->em->persist($specifications);
                }
            }

            if($images != null){
                foreach($images as $img){
                    $img->setItemsdetails($itemDetails);
                    $this->em->persist($img);
                }
            }

            // creates item rows with different statuses
            $itemStatuses = $this->em->getRepository('KTUShopBundle:Itemstatuses');
            for($i=1; $i<5; $i++){
                $item = new Items();
                $item->setItemsdetails($itemDetails);
                $item->setQuantity(0);
                $item->setItemstatuses($itemStatuses->find($i));
                $this->em->persist($item);
            }


            $this->em->flush();

            return $itemDetails;
        }
        else{
            throw new \Exception('No data given.');
        }

    }

    public function editItem($id, $data, $images, $removeImg)
    {
        if($data != null){
            $itemDetails = $this->em->getRepository('KTUShopBundle:Itemsdetails')->find($id);

            if($itemDetails !=null){

                // Update item details fields
                $itemDetails->setName($data['name']);
                $itemDetails->setDescription($data['description']);
                $itemDetails->setCategories($data['category']);
                $itemDetails->setPrice($data['price']);
                $itemDetails->setDiscount($data['discount']);
                $this->em->persist($itemDetails);
                $this->em->flush();

                // Remove old specs
                $specs = $this->em->getRepository('KTUShopBundle:Specifications')->findByItemsdetails($itemDetails);
                foreach($specs as $spec){
                    $this->em->remove($spec);
                }

                // Add new specs
                $specsCount = count($data['specs_names']);

                if($specsCount > 0){
                    for($i = 0; $i < $specsCount; $i++)
                    {
                        $specifications = new Specifications();
                        $specifications->setItemsdetails($itemDetails);
                        $specifications->setName($data['specs_names'][$i]);
                        $specifications->setValue($data['specs_values'][$i]);
                        $this->em->persist($specifications);
                    }
                }

                // Remove selected images
                if($removeImg != null){
                    $imgRep = $this->em->getRepository('KTUShopBundle:Images');

                    foreach($removeImg as $removePath){
                        $this->em->remove($imgRep->findOneByPath($removePath));
                    }
                }

                // Add new images
                if($images != null){
                    foreach($images as $img){
                        $img->setItemsdetails($itemDetails);
                        $this->em->persist($img);
                    }
                }

                $this->em->flush();

                return true;
            }
            else{
                throw new \Exception('Items details with id: '.$id.' not found.');
            }

        }
        else{
            throw new \Exception('No data given.');
        }

    }

    public function removeItem($id)
    {
        $itemDetails = $this->em->getRepository('KTUShopBundle:Itemsdetails')->find($id);

        if($itemDetails != null){

            $imgs = $this->em->getRepository('KTUShopBundle:Images')->findByItemsdetails($itemDetails);

            // we need to unlink the image files before removing items details
            foreach($imgs as $i){
                $i->removeUpload();
            }

            // check if itemDetails is in purchases
            $itemDetailsCount = $this->em->getRepository('KTUShopBundle:Purchases')->findItemDetails($id);

            // do clean up work
            if(count($itemDetailsCount) > 0){
                $itemDetails->setIsRemoved(1);
                $itemDetails->setCategories(null);
                $itemDetails->setPrice(0);
                $itemDetails->setDiscount(0);
                $itemsStored = $this->em->getRepository('KTUShopBundle:Items')->findItemByStatusAndItemsdetails($id, 1);
                $itemsStored->setQuantity(0);
            }
            // if not just remove
            else{
                $this->em->remove($itemDetails);
            }
            $this->em->flush();

            return true;
        }
        else{
            throw new \Exception('Items details with id: '.$id.' not found.');
        }
    }

    public function addToStorage($id, $count)
    {
        $itemDetails = $this->em->getRepository('KTUShopBundle:Itemsdetails')->find($id);

        if($itemDetails != null){

            $item = $this->em->getRepository('KTUShopBundle:Items')->findItemByStatusAndItemsdetails($id, 1);
            $stored = $item->getQuantity();
            $itemCount = $stored+$count;
            $item->setQuantity($itemCount);
            $this->em->persist($item);

            $this->em->flush();

            return $itemCount;
        }
        else{
            throw new \Exception('Items details with id: '.$id.' not found.');
        }
    }

    public function removeFromStorage($id, $count)
    {
        $itemDetails = $this->em->getRepository('KTUShopBundle:Itemsdetails')->find($id);

        if($itemDetails != null){

            $item = $this->em->getRepository('KTUShopBundle:Items')->findItemByStatusAndItemsdetails($id, 1);
            $stored = $item->getQuantity();
            $itemCount = $stored-$count;
            $item->setQuantity($itemCount);
            $this->em->persist($item);

            $this->em->flush();

            return $itemCount;
        }
        else{
            throw new \Exception('Items details with id: '.$id.' not found.');
        }

    }

    public function addToStatus($id, $count, $status)
    {
        $itemDetails = $this->em->getRepository('KTUShopBundle:Itemsdetails')->find($id);

        if($itemDetails != null){

            $item = $this->em->getRepository('KTUShopBundle:Items')->findItemByStatusAndItemsdetails($id, $status);
            $existingQuantity = $item->getQuantity();
            $item->setQuantity($existingQuantity + $count);
            $this->em->persist($item);

            $this->em->flush();

            return true;
        }
        else{
            throw new \Exception('Items details with id: '.$id.' not found.');
        }
    }

    public function removeFromStatus($id, $count, $status)
    {
        $itemDetails = $this->em->getRepository('KTUShopBundle:Itemsdetails')->find($id);

        if($itemDetails != null){

            $item = $this->em->getRepository('KTUShopBundle:Items')->findItemByStatusAndItemsdetails($id, $status);
            $existingQuantity = $item->getQuantity();
            if($existingQuantity < $count){
                $item->setQuantity(0);
            }
            else{
                $item->setQuantity($existingQuantity - $count);
            }

            $this->em->persist($item);

            $this->em->flush();

            return true;
        }
        else{
            throw new \Exception('Items details with id: '.$id.' not found.');
        }
    }

    public function manageDiscount($id, $discount)
    {
        $itemDetails = $this->em->getRepository('KTUShopBundle:Itemsdetails')->find($id);

        if($itemDetails != null){

            $itemDetails->setDiscount($discount);

            $this->em->persist($itemDetails);
            $this->em->flush();

            return $itemDetails;
        }
        else{
            throw new \Exception('Items details with id: '.$id.' not found.');
        }
    }

    // Check if the item number is valid to be stored
    public function checkStorage($id, $count, $remove=false)
    {
        $stored = $this->em->getRepository('KTUShopBundle:Items')->findItemByStatusAndItemsdetails($id, 1)->getQuantity();

        if($remove == true){
            $sumStored = $stored-$count;
        }
        else{
            $sumStored = $stored+$count;
        }

        if($sumStored > self::MAX_STORED){
            return 'Maksimaliai sandėlyje galima patalpinti '.self::MAX_STORED.' prekių. Pateikta: '.$count.'.';
        }
        else if($sumStored < 0){
            return 'Sandėlyje yra '.$stored.'. Negalima išimti '.$count.' prekės(-ių).';
        }

        return true;
    }

} 