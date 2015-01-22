<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ItemsRepository extends EntityRepository
{

    function findItemByStatusAndItemsdetails($id, $status)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('i')
            ->from('KTUShopBundle:Items', 'i')
            ->where('i.itemsdetails='.$id)
            ->andWhere('i.itemstatuses='.$status)
        ;

        return $results = $qb->getQuery()->getResult()[0];
    }

    function findAvailableItems()
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('i')
            ->from('KTUShopBundle:Items', 'i')
            ->where('i.itemstatuses = ' . '1')
            ->andWhere('i.quantity > ' . '0');

        $results = $qb->getQuery()->getResult();

        return $results;
    }

    function findAvailableItemsCount(){
        $count = sizeof($this->findAvailableItems());

        return $count;
    }


    function findItemsByPage($page, $category = NULL){
        $em = $this->getEntityManager();

        $sliceFrom = ($page - 1) * 6 ;

        if( $category == NULL ){
            $itemEntities = $em->getRepository('KTUShopBundle:Items')->findByitemstatuses(1);
        }
        else{
            $itemDetails = $em->getRepository('KTUShopBundle:Itemsdetails')->findBycategories( $category );
            $itemEntities = $em->getRepository('KTUShopBundle:Items')->findByitemsdetails( $itemDetails );
            $Detailsarr = array();
            $Itemsarr = array();
            foreach( $itemEntities as $item ){
                if( !( in_array($item->getItemsdetails()->getId(), $Detailsarr) ) ){
                    array_push( $Detailsarr, $item->getItemsdetails()->getId() );
                    array_push( $Itemsarr, $item );
                }
            }

            $itemEntities = $Itemsarr;
        }


        $temp = array();
        foreach($itemEntities as $item){
            $statusID = $item->getItemstatuses()->getId();
            $itemQuantity = $item->getQuantity();

            if( ( $statusID == 1 && $itemQuantity > 0 ) ){
                array_push( $temp, $item );
            }
        }
        $itemEntities = $temp;

        $itemEntities = array_slice( $itemEntities, $sliceFrom, 6);

        return $itemEntities;
    }

} 