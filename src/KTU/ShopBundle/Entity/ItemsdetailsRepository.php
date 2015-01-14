<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ItemsdetailsRepository extends EntityRepository
{

    public function findItemQuantity($id, $status=1)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('i.quantity as quantity')
            ->from('KTUShopBundle:Items', 'i')
            ->where('i.itemsdetails='.$id)
            ->andWhere('i.itemstatuses='.$status)
        ;

        $results = $qb->getQuery()->getResult();
        if($results)
            return (int) $results[0]['quantity'];
        else
            return null;
    }

    public function findItemSentQuantity($id)
    {
        return $this->findItemQuantity($id, 4);
    }

    public function findItemPendingQuantity($id)
    {
        return $this->findItemQuantity($id, 3);
    }

    public function findItemSoldQuantity($id)
    {
        return $this->findItemQuantity($id, 2);
    }

    public function findItemRating($id)
    {

        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('AVG(r.rating) as rating')
            ->from('KTUShopBundle:Ratings', 'r')
            ->where('r.itemsdetails='.$id)
        ;

        $results = $qb->getQuery()->getResult();
        if($results)
            return (double) $results[0]['rating'];
        else
            return null;

    }

    public function findItemRatersCount($id)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('count(r.rating) as ratersCount')
            ->from('KTUShopBundle:Ratings', 'r')
            ->where('r.itemsdetails='.$id)
        ;

        $results = $qb->getQuery()->getResult();
        if($results)
            return (double) $results[0]['ratersCount'];
        else
            return null;
    }

} 