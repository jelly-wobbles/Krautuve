<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ShoppingcartsRepository extends EntityRepository{


    /**
     * @param $userID
     * @return array of cart items of specified user
     */
    public function findUsersCartItems($userID)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('sc')
            ->from('KTUShopBundle:Shoppingcarts', 'sc')
            ->where('sc.users=' . $userID);

        $results = $qb->getQuery()->getResult();

        return $results;
    }


    /**
     * @param $userID
     * @return number of cart items of specified user
     */
    public function findUsersCartItemsCount($userID)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('sc')
            ->from('KTUShopBundle:Shoppingcarts', 'sc')
            ->where('sc.users=' . $userID);

        $results = $qb->getQuery()->getResult();
        $results = sizeof($results);

        return $results;
    }


} 