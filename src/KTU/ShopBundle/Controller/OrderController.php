<?php

namespace KTU\ShopBundle\Controller;

use KTU\ShopBundle\Entity\Itemspurchases;
use KTU\ShopBundle\Entity\Purchases;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
Use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;

//Debug::dump($object);

class OrderController extends Controller
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

        $totalPriceEU = $this->getUsersCartPrice($user);
        $totalPriceLT = $totalPriceEU * 3.4528;

        return $this->render('KTUShopBundle:Order:index.html.twig',
            array(
                'cartItems' => $cartItems,
                'cartCount' => $cartCount,
                'totalPriceEU' => $totalPriceEU,
                'totalPriceLT' => $totalPriceLT,
                'user' => $user,
            ));
    }


    public function validateAction(Request $request, $id){

        $valid = -1;
        $value = $request->request->get('value');
        $inputID = $request->request->get('inputID');

        if( $inputID == "number" ){
            $formats = array(
                '+###########', '###########',
                '#########', '# ### #####',
                '+### ### #####', '### ### #####'
            );

            $format = trim(preg_replace('/[0-9]/', '#', $value));
            if( in_array($format, $formats) ){
                $valid = $inputID;
            }
        }


        if( $inputID == "name" || $inputID == "surname" || $inputID == "address"){
            if ( !preg_match('/[^A-Za-z\-]/', $value) )
            {
                $valid = $inputID;
            }
        }

        if( $inputID == "address"){
            if ( !preg_match('/[^A-Za-z0-9\-]/', $value) )
            {
                $valid = $inputID;
            }
        }


        return new Response( $valid );
    }




    public function confirmAction(Request $request, $id){

        $hasChanges = false;
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();


        $user = $em->getRepository('KTUShopBundle:Users')->findOneByid( $id );

        $number = $request->request->get('number');
        $address = $request->request->get('address');
        $name = $request->request->get('name');
        $surname = $request->request->get('surname');

        $currentNumber = $user->getPhoneNumber();
        $currentAddress = $user->getAddress();
        $currentName = $user->getName();
        $currentSurname = $user->getSurname();

        if( $number != $currentNumber ){
            $user->setPhoneNumber( $number );
            $hasChanges = true;
        }

        if( $address != $currentAddress ){
            $user->setAddress( $address );
            $hasChanges = true;
        }

        if( $name != $currentName ){
            $user->setName( $name );
            $hasChanges = true;
        }

        if( $surname != $currentSurname ){
            $user->setSurname( $surname );
            $hasChanges = true;
        }

        if( $hasChanges ){
            $em->flush();
        }

        return new Response( 1 );
    }


    //////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////

    private function checkUser($id){
        $user = $this->get('security.context')->getToken()->getUser();

        if( $user->getId() != $id ){
            return false;
        }
        else{
            return true;
        }
    }


    private function getUsersCartPrice($user){
        $em = $this->getDoctrine()->getManager();
        $totalPrice = 0;

        $cartItems = $em->getRepository('KTUShopBundle:Shoppingcarts')->findByusers( $user );

        if( $cartItems ){
            foreach( $cartItems as $cItem ){
                $itemPrice = $cItem->getItems()->getItemsdetails()->getPrice();
                $quantity = $cItem->getQuantity();

                $totalPrice += ( $itemPrice * $quantity );
            }

        }

        return ( (float)$totalPrice );
    }

}
