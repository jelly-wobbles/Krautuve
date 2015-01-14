<?php

namespace KTU\ShopBundle\Controller;


use KTU\ShopBundle\Entity\Itemspurchases;
use KTU\ShopBundle\Entity\Purchases;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;

class PayseraPaymentsController extends Controller
{

    public function redirectToPaymentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $purchaseSum = 0;

        if($user == "anon."){
            return $this->redirect($this->generateUrl('shop_landingpage'));
        }


        $user = $em->getRepository('KTUShopBundle:Users')->findOneByid( $user->getId() );


        $purchasesArr = $em->getRepository('KTUShopBundle:Purchases')->findByusers( $user );

        if( $purchasesArr ){
            foreach( $purchasesArr as $purchaseObj ){
                if( $purchaseObj->getPurchasestatuses()->getId() == 3 ){
                    $em->remove( $purchaseObj );
                }
            }
        }


        $userName = $user->getName();
        $userSurname = $user->getSurname();
        $userEmail = $user->getEmail();

        $baseurl = $request->getScheme() . '://' . $request->getHttpHost();

        $acceptUrl = $baseurl.$this->generateUrl('paysera.accept');
        $cancelUrl = $baseurl.$this->generateUrl('paysera.cancel');
        $callbackUrl = $baseurl.$this->generateUrl('paysera.callback');

        $statusProgressObj = $em->getRepository('KTUShopBundle:Purchasestatuses')->findOneByid( 3 );

        $newPurchaseObj = new Purchases();
        $newPurchaseObj->setPurchasestatuses( $statusProgressObj );
        $newPurchaseObj->setUsers( $user );
        $newPurchaseObj->setDate( new DateTime() );
        $em->persist( $newPurchaseObj );
        $em->flush();

        $newPurchaseID = $newPurchaseObj->getId();

        $cartItems = $em->getRepository('KTUShopBundle:Shoppingcarts')->findByusers( $user );


        foreach( $cartItems as $cartItem ){
            $itemQuantity = $cartItem->getItems()->getQuantity();

            if( $cartItem->getQuantity() > $itemQuantity ){
                if(  $itemQuantity > 0 ){
                    $cartItem->setQuantity( $itemQuantity );
                }
                else{
                    $em->remove($cartItem);
                }

                $em->flush();
                return new Response( $user->getId() );
            }
        }


        if( $cartItems )
        {
            foreach( $cartItems as $item ){
                $purchaseSum += $item->getItems()->getItemsdetails()->getPrice() * $item->getQuantity();
            }
            $purchaseSum *= 100;
        }
        else{
            return $this->redirect($this->generateUrl('shop_landingpage'));
        }




        $url = $this->container->get('evp_web_to_pay.request_builder')->buildRequestUrlFromData(array(
            'orderid' => $newPurchaseID,
            'amount' => $purchaseSum,
            'currency' => 'EUR',
            'country' => 'LT',
            'accepturl' => $acceptUrl,
            'cancelurl' => $cancelUrl,
            'callbackurl' => $callbackUrl,
            'test' => 1,
            'p_name' => $userName,
            'p_surname' => $userSurname,
            'p_email' => $userEmail
        ));

        return new RedirectResponse($url);
    }

    public function acceptAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('KTUShopBundle:Users')->findOneByid( $user->getId() );

        $purchasesArr = $em->getRepository('KTUShopBundle:Purchases')->findByusers( $user );

        if( $purchasesArr ){
            $error = false;

            foreach( $purchasesArr as $purchaseObj ){
                if( $purchaseObj->getPurchasestatuses()->getId() == 3 ){
                    $em->remove( $purchaseObj );
                    $error = true;
                }
            }

            if($error){
                return $this->render('KTUShopBundle:baseView:notify.html.twig',
                    array(
                        'status' => -1,
                        'title' => 'Klaida',
                        'text' => 'Nepavyko įvykdyti pirkimo, mokėjimas neatliktas.',
                        'redirectText' => null,
                        'redirectUrl' => $this->generateUrl('shop_landingpage'),
                        'timer' => 20,
                    ));
            }
        }


        /*
         * status:
         *      1 => confirmation (green)
         *      -1 => error (red)
         *
         * title:
         *      string => set title text
         *      null => hide title block
         *
         * text:
         *      string => set message text
         *      null => hide message field
         *
         * redirectText:
         *      string => set comment for user where click will redirect
         *      null => default redirect text 'Spragtelėkite, jeigu norite tęsti'
         *
         * redirectUrl:
         *      string => url to redirect to on click
         *
         * timer:
         *      integer => set time in seconds after which user will be automatically redirected to redirectUrl
         *      null => don't redirect automatically, only after click
         *
         */
        return $this->render('KTUShopBundle:baseView:notify.html.twig',
            array(
                'status' => 1,
                'title' => 'Apmokėjimas atliktas',
                'text' => null,
                'redirectText' => null,
                'redirectUrl' => $this->generateUrl('shop_landingpage'),
                'timer' => 10,
            ));
    }

    public function cancelAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        if($user == "anon."){
            return $this->redirect($this->generateUrl('shop_landingpage'));
        }

        $user = $em->getRepository('KTUShopBundle:Users')->findOneByid( $user->getId() );

        $purchasesArr = $em->getRepository('KTUShopBundle:Purchases')->findByusers( $user );

        if( $purchasesArr ){
            foreach( $purchasesArr as $purchaseObj ){
                if( $purchaseObj->getPurchasestatuses()->getId() == 3 ){
                    $em->remove( $purchaseObj );
                }
            }
            $em->flush();
        }

        return $this->redirect($this->generateUrl('shop_landingpage'));
    }

    public function callbackAction(Request $request)
    {
        try {
            $callbackValidator = $this->get('evp_web_to_pay.callback_validator');
            $data = $callbackValidator->validateAndParseData($this->getRequest()->query->all());
            if ($data['status'] == 1) {
                // Provide your customer with the service
                //$request->query->get('wp_orderid');
                $em = $this->getDoctrine()->getManager();

                $userEmail = $data['p_email'];
                $orderID = $data['orderid'];

                $user = $em->getRepository('KTUShopBundle:Users')->findOneByemail( $userEmail );
                $cartItems = $em->getRepository('KTUShopBundle:Shoppingcarts')->findByusers( $user );
                $newPurchaseObj = $em->getRepository('KTUShopBundle:Purchases')->findOneByid( $orderID );
                $statusConfirmedObj = $em->getRepository('KTUShopBundle:Purchasestatuses')->findOneByid( 2 );

                $newPurchaseObj->setPurchasestatuses( $statusConfirmedObj );


              foreach( $cartItems as $cartItem ){
                  $item = $cartItem->getItems();
                  $itemAvailableQuantity = $item->getQuantity();
                  $itemCartQuantity = $cartItem->getQuantity();
                  $itemPrice = $item->getItemsdetails()->getPrice();

                  $item->setQuantity( $itemAvailableQuantity - $itemCartQuantity );

                  $arr = $em->getRepository('KTUShopBundle:Items')->findByitemsdetails( $item->getItemsdetails() );
                  foreach( $arr as $a ){
                      if( $a->getItemstatuses()->getId() == 2 ){
                          $oldQuantity = $a->getQuantity();
                          $newQuantity = $oldQuantity + $itemCartQuantity;
                          $a->setQuantity( $newQuantity );
                      }
                  }

                  $newItemsPurchases = new Itemspurchases();
                  $newItemsPurchases->setItems($item);
                  $newItemsPurchases->setPurchases($newPurchaseObj);
                  $newItemsPurchases->setItemprice($itemPrice);
                  $newItemsPurchases->setQuantity($itemCartQuantity);
                  $em->persist( $newItemsPurchases );

                  $em->remove($cartItem);
              }

              $em->flush();

              return new Response('OK');
            }
        } catch (\Exception $e) {
            //handle the callback validation error here

            return new Response($e->getTraceAsString(), 500);
        }
    }

}