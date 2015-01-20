<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;

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

    public function dropItem($userID, $itemID){
        $em = $this->em;

        $text = 'SELECT p
            FROM KTUShopBundle:Shoppingcarts p
            WHERE p = '. $itemID;

        $query = $em->createQuery($text);

        $item = $query->getSingleResult();

        if($item)
        {
            $em->remove( $item );
            $em->flush();
            $amount = $cartCount = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartItemsCount($userID);

            if( $amount > 0 ) {
                return new Response( $amount );
            }
            else{
                return $this->redirect($this->generateUrl('shop_landingpage'));
            }
        }
        else
            return new Response( -1 );
    }

} 