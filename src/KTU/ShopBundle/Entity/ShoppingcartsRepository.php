<?php

namespace KTU\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ShoppingcartsRepository extends EntityRepository{


    /**
     * @param $userID
     * @return number of cart items of specified user
     */
    public function findUsersCartItemsCount($userID)
    {
        $count = 0;
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('sc.quantity')
            ->from('KTUShopBundle:Shoppingcarts', 'sc')
            ->where('sc.users=' . $userID);

        $results = $qb->getQuery()->getResult();

        if( $results ){
            foreach( $results as $result ){
                $count = $count + $result['quantity'];
            }
        }

        return $count;
    }


    /**
     * @param $userID
     * @return float total cart price
     */
    public function findUsersCartPrice($userID)
    {
        $price = 0;

        $cartItems = $this->findByusers( $userID );

        if( $cartItems ){
            foreach( $cartItems as $cItem ){
                $itemPrice = $cItem->getItems()->getItemsdetails()->getPrice();
                $quantity = $cItem->getQuantity();

                $price += ( $itemPrice * $quantity );
            }

        }

        return ( (float)$price );
    }


} 