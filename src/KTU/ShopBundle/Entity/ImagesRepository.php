<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ImagesRepository extends EntityRepository
{
    public function findByItemsArray($itemEntities){

        $em = $this->getEntityManager();
        $imageEntities = array();

        foreach ($itemEntities as $item){
            $detailsObj = $item->getItemsdetails();
            $imageObj = $em->getRepository('KTUShopBundle:Images')->findByitemsdetails( $detailsObj );

            if($imageObj){
                $imageObj = array_pop($imageObj);
                array_push( $imageEntities, $imageObj );
            }
        }

        return $imageEntities;
    }
} 