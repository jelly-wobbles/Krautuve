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
        $result = (float) $result['average'];

        return $result;
    }


    public function findUsersRating( $user, $itemsDetails ){
        $em = $this->getEntityManager();
        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();

        $qb = $em->createQueryBuilder();
        $qb->select('r.rating as rating')
            ->from('KTUShopBundle:Ratings', 'r')
            ->where('r.itemsdetails = '. $itemsDetails->getId() . ' and r.users = ' . $user->getId() );

        $result = $qb->getQuery()->getSingleResult();
        $usersRating = (int) $result['rating'];

        return $usersRating;
    }


} 