<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KTU\ShopBundle\Entity\Shoppingcarts;
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


    /**
     * @param $userID
     * @param %itemID
     *
     * Drops one item from cart
     *
     * @return Response
     */
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


    /**
     * @param $userID
     * @param %itemID
     *
     * Adds item to cart
     *
     * @return Response
     */
    public function addToCart($userID, $itemID){

        $em = $this->em;

        $item = $em->getRepository('KTUShopBundle:Items')->find( $itemID );
        $user = $em->getRepository('KTUShopBundle:Users')->findOneByid( $userID );

        if( !($item) ){
            return new Response( -1 );
        }

        $shopAmount = $item->getQuantity();
        $cartAmount = 0;
        $amount = 0;

        $cartItemsArr = $em->getRepository('KTUShopBundle:Shoppingcarts')->findByusers( $user );
        $cartItem = null;

        if( sizeof($cartItemsArr) > 0 ){
            foreach( $cartItemsArr as $cItem ){
                if( $cItem->getItems()->getId() == $item->getId() ){
                    $cartItem = $cItem;
                }
            }
        }

        if( $cartItem ){
            $cartAmount = $cartItem->getQuantity();

            if( $cartAmount >= $shopAmount ){
                return new Response( -1 );
            }

            $oldQuantity = $cartItem->getQuantity();
            $cartItem->setQuantity( $oldQuantity + 1 );
            $em->flush();
        }
        else{
            if( $shopAmount > 0 ){
                $cartItem = new Shoppingcarts();

                $cartItem->setUsers( $user );
                $cartItem->setItems( $item );
                $cartItem->setQuantity( 1 );

                $em->persist( $cartItem );
                $em->flush();
            }
        }

        $amount = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartItemsCount($userID);

        return new Response( $amount );

    }


} 