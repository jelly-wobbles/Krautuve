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

} 