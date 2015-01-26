<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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
    }


} 