<?php

namespace KTU\ShopBundle\Entity;


use Doctrine\ORM\EntityRepository;

class RatingsRepository extends EntityRepository
{

    public function findItemsDetailsAverage( $itemsDetailsID )
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('avg(r.rating) as average')
            ->from('KTUShopBundle:Ratings', 'r')
            ->where('r.itemsdetails = '. $itemsDetailsID);

        $result = $qb->getQuery()->getSingleResult();

        return $result;
    }

} 