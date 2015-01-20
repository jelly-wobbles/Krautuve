<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class CartEditor {

    private $em;
    private $cartItemsArr;

    public function __construct(EntityRepository $cartItemsArr, EntityManager $em){
        $this->cartItemsArr = $cartItemsArr;
        $this->em = $em;
    }


    /**
     * @param $userID
     *
     * Clears the cart of specified user by his ID
     *
     * @return bool
     */
    public function clearUsersCart($userID)
    {

        $qb = $this->em->createQueryBuilder();

        $qb->delete('KTUShopBundle:Shoppingcarts', 'sc')
            ->where('sc.users=' . $userID);

        $qb->getQuery()->execute();

    }

} 