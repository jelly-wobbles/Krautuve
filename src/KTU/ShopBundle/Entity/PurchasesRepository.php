<?php

namespace KTU\ShopBundle\Entity;


use Doctrine\ORM\EntityRepository;

class PurchasesRepository extends EntityRepository
{

    public function findItemQuantity($id)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('sum(ip.quantity) as quantity')
            ->from('KTUShopBundle:Itemspurchases', 'ip')
            ->where('ip.purchases='.$id);

        $results = $qb->getQuery()->getResult();
        if($results)
            return (int) $results[0]['quantity'];
        else
            return null;
    }

    public function findItemDetails($id)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('idet')
            ->from('KTUShopBundle:Itemsdetails', 'idet')
            ->join('idet.items', 'it')
            ->join('it.itemspurchases', 'ip')
            ->where('it.itemsdetails ='.$id);

        return $results = $qb->getQuery()->getResult();
    }

    public function findPurchaseSum($id)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('sum(ip.itemprice * ip.quantity) as price')
            ->from('KTUShopBundle:Itemspurchases', 'ip')
            ->where('ip.purchases='.$id);

        $results = $qb->getQuery()->getResult();

        if($results)
            return (double) $results[0]['price'];
        else
            return null;
    }

} 