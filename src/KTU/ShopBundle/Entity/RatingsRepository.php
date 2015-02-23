<?php

namespace KTU\ShopBundle\Entity;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class RatingsRepository extends EntityRepository
{

    /**
     * @param $itemsDetailsID
     *
     * Find item's average rating
     *
     * @return float
     */
    public function findItemsDetailsAverage( $itemsDetailsID )
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('avg(r.rating) as average')
            ->from('KTUShopBundle:Ratings', 'r')
            ->where('r.itemsdetails = :itemsDetailsID')
            ->setParameter('itemsDetailsID', $itemsDetailsID);

        $result = $qb->getQuery()->getSingleResult();
        $result = (float) $result['average'];

        return $result;
    }


    /**
     * @param $user
     * @param $itemsDetails
     *
     * Find specified user's rating on the specified item
     *
     * @return int
     */
    public function findUsersRating( $user, $itemsDetails ){
        $em = $this->getEntityManager();
        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();

        $qb = $em->createQueryBuilder();

        $parameters = array(
            'itemsDetailsID' => $itemsDetails->getId(),
            'usersID' => $user->getId()
        );

        $qb->select('r.rating as rating')
            ->from('KTUShopBundle:Ratings', 'r')
            ->where('r.itemsdetails = :itemsDetailsID and r.users = :usersID')
            ->setParameters($parameters);

        $result = $qb->getQuery()->getSingleResult();
        $usersRating = (int) $result['rating'];

        return $usersRating;
    }


} 