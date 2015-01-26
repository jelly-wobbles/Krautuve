<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KTU\ShopBundle\Entity\Ratings;

class RatingsEditor {

    private $em;
    private $ratings;

    public function __construct(EntityRepository $ratings, EntityManager $em){
        $this->ratings = $ratings;
        $this->em = $em;
    }


    /**
     * @param $userID
     * @param $itemsDetailsID
     *
     * removes a rating
     *
     * @return bool
     * @throws /Exception Not found
     */
    public function removeRating($userID, $itemsDetailsID){
        $qb = $this->em->createQueryBuilder();

        $qb->delete('KTUShopBundle:Ratings', 'r')
            ->where('r.users = ' . $userID . ' and r.itemsdetails = ' . $itemsDetailsID);

        $qb->getQuery()->execute();

        return true;
    }


    /**
     * @param $user
     * @param $itemsDetails
     * @param $value
     *
     * adds a rating
     *
     * @return bool
     * @throws /Exception Not found
     */
    public function addRating($user, $itemsDetails, $value){

        $ratingObj = new Ratings();
        $ratingObj->setRating( $value );
        $ratingObj->setUsers( $user );
        $ratingObj->setItemsdetails( $itemsDetails );

        $this->em->persist( $ratingObj );
        $this->em->flush();

        return true;
    }


    /**
     * @param $itemsDetails
     *
     * check if itemsDetails has ratings
     *
     * @return bool
     */
    public function hasRatings($itemsDetails){
        $em = $this->em;

        $qb = $em->createQueryBuilder();
        $qb->select('count(r) as amount')
            ->from('KTUShopBundle:Ratings', 'r')
            ->where('r.itemsdetails = '. $itemsDetails->getId() );

        $result = $qb->getQuery()->getSingleResult();
        $result = (int) $result['amount'];

        if( $result > 0 ){
            $hasRatings = true;
        }
        else{
            $hasRatings = false;
        }

        return $hasRatings;
    }


    /**
     * @param $itemsDetails
     * @param $user
     *
     * check if a user has already rated itemsdetails
     *
     * @return bool
     */
    public function hasRated($user, $itemsDetails){
        $em = $this->em;

        $qb = $em->createQueryBuilder();
        $qb->select('count(r) as amount')
            ->from('KTUShopBundle:Ratings', 'r')
            ->where('r.itemsdetails = '. $itemsDetails->getId() . ' and r.users = ' . $user->getId() );

        $result = $qb->getQuery()->getSingleResult();
        $result = (int) $result['amount'];

        if( $result > 0 ){
            $hasRated = true;
        }
        else{
            $hasRated = false;
        }

        return $hasRated;
    }
} 