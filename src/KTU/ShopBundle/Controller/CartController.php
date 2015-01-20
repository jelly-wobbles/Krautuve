<?php

namespace KTU\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
Use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Response;
//Debug::dump($object);

class CartController extends Controller
{
    public function indexAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $userID = $user->getId();

        if( $this->checkUser($id) == false ){
            return $this->redirect( $this->generateUrl('shop_landingpage') );
        }

        $cartCount = 0;
        $totalPriceEU = 0;
        $totalPriceLT = 0;
        $cartItems = $em->getRepository('KTUShopBundle:Shoppingcarts')->findByusers( $user );


        if( $cartItems )
        {
            $cartCount = $cartCount = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartItemsCount($userID);
        }
        else{
            return $this->redirect($this->generateUrl('shop_landingpage'));
        }


        $totalPriceEU = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartPrice( $userID );

        return $this->render('KTUShopBundle:Cart:index.html.twig',
            array(
                'cartItems' => $cartItems,
                'cartCount' => $cartCount,
                'totalPriceEU' => $totalPriceEU,
            ));
    }



    public function dropAction($id, $idItem){

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $userID = $user->getId();

        $text = 'SELECT p
            FROM KTUShopBundle:Shoppingcarts p
            WHERE p = '. $idItem;


        $query = $em->createQuery(
            $text
        );

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




    public function clearAction(){

        $cartEditor = $this->container->get('cart.editor');
        $user = $this->get('security.context')->getToken()->getUser();
        $userID = $user->getId();
        $cartEditor->clearUsersCart( $userID );

        return new Response(1);
    }


    public function quantityChangeAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $newQuantity = $request->request->get('newQuantity');
        $id = $request->request->get('id');
        $user = $em->getRepository('KTUShopBundle:Users')->find( $user->getId() );
        $userID = $user->getId();
        $item = $em->getRepository('KTUShopBundle:Items')->findOneByid( $id );
        $cartItems = $em->getRepository('KTUShopBundle:Shoppingcarts')->findByusers( $user );
        $cartItem = null;
        $cartCount = 0;

        if( !($user) || !($item) || !($cartItems) || $newQuantity < 1){
            return new Response( 0 );
        }

        $shopAmount = $item->getQuantity();

        $cartCount = $cartCount = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartItemsCount($userID);

        foreach( $cartItems as $cItem ){
            if( $cItem->getItems()->getId() == $id ){
                $cartItem = $cItem;
            }
        }

        if( $newQuantity > $shopAmount ){
            return new Response( 0 - $shopAmount );
        }

        if( $cartItem ){
            $oldQuantity = $cartItem->getQuantity();
            $cartItem->setQuantity( $newQuantity );
            $em->flush();

            $cartCount += $newQuantity - $oldQuantity ;
        }


        return new Response( $cartCount );
    }

    public function recalculatePricesQuantityAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');

        $user = $this->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('KTUShopBundle:Users')->find( $user->getId() );

        $item = $em->getRepository('KTUShopBundle:Items')->find( $id );
        $cartItems = $em->getRepository('KTUShopBundle:Shoppingcarts')->findByusers( $user );
        $cartItem = null;

        foreach( $cartItems as $cItem ){
            if( $cItem->getItems()->getId() == $item->getId() ){
                $cartItem = $cItem;
            }
        }

        if( !($item) || !($cartItem) ){
            return new Response( -1 );
        }

        $itemPrice = $item->getItemsdetails()->getPrice();
        $cartItemQuantity = $cartItem->getQuantity();

        $newPrice = $itemPrice * $cartItemQuantity;

        return new Response( (float)$newPrice );
    }



    public function recalculateTotalPriceAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $totalPrice = 0;

        $user = $this->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('KTUShopBundle:Users')->find( $user->getId() );
        $userID = $user->getId();

        $cartItems = $em->getRepository('KTUShopBundle:Shoppingcarts')->findByusers( $user );

        if( !($cartItems) ){
            return new Response( -1 );
        }

        $totalPrice = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartPrice( $userID );

        return new Response( (float)$totalPrice );
    }

    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////

    private function checkUser($id){
        $user = $this->get('security.context')->getToken()->getUser();

        if( $user->getId() != $id ){
            return false;
        }
        else{
            return true;
        }
    }


}
