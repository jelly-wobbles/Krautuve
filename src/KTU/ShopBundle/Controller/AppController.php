<?php

namespace KTU\ShopBundle\Controller;

use KTU\ShopBundle\Entity\Ratings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
Use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
//Debug::dump($object);

class AppController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $code = $request->request->get('code');

        if( $code != "lkanjkg272&T@&T@T%@&Jjkf5a1f68541fa65d1fa56d41af65af4ds664@@@*^&#&Q)(@*^&#*Ejgbnsajkgbndjkgbs" ){
            return new Response(-1);
        }

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->loadUserByUsername($username);
        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $encodedPass = $encoder->encodePassword($password, $user->getSalt());
        $valid = ($user->getPassword() === $encodedPass);

        if( $valid == 1 )
        {
            $purchasesIDArr = array();
            $purchasesArr = $em->getRepository('KTUShopBundle:Purchases')->findByusers( $user );

            if( $purchasesArr ){
                foreach( $purchasesArr as $purchase ){
                    array_push($purchasesIDArr, $purchase->getId());
                }

                return new JsonResponse( $purchasesIDArr );
            }
            else{
                $resp = array();
                array_push( $resp, -1 );
                return new JsonResponse( $resp );
            }
        }
        else{
            $resp = array();
            array_push( $resp, 0 );
            return new JsonResponse( $resp );
        }

    }

    public function getPurchaseInfoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $code = $request->request->get('code');

        if( $code != "lkanjkg272&T@&T@T%@&Jjkf5a1f68541fa65d1fa56d41af65af4ds664@@@*^&#&Q)(@*^&#*Ejgbnsajkgbndjkgbs" ){
            return new Response(-1);
        }

        $purchasePrice = 0;
        $itemsCount = 0;
        $itemsPricesArr = array();
        $itemsRatingsArr = array();

        $purchaseObj = $em->getRepository('KTUShopBundle:Purchases')->findOneByid( $id );

        if( !($purchaseObj) ){
            $dataArr = array(
                "status" => "error",
            );

            return new JsonResponse( $dataArr );
        }

        $itemsPurchasesArr = $em->getRepository('KTUShopBundle:Itemspurchases')->findBypurchases( $purchaseObj );

        $status = $purchaseObj->getPurchasestatuses()->getStatus();
        $date = $purchaseObj->getDate();
        $itemsCount = sizeof($itemsPurchasesArr);

        if( $itemsPurchasesArr ){
            foreach( $itemsPurchasesArr as $itemsPurchase ){
                $price = $itemsPurchase->getItemprice();
                $name = $itemsPurchase->getItems()->getItemsdetails()->getName();
                $itemsDetails = $itemsPurchase->getItems()->getItemsdetails();

                $ratingsArr = $em->getRepository('KTUShopBundle:Ratings')->findByitemsdetails( $itemsDetails );

                $rating = -1;

                if( $ratingsArr ){
                    $ratingsCount = sizeof( $ratingsArr );
                    $sum = 0;
                    foreach( $ratingsArr as $rating ){
                        $sum += $rating->getRating();
                    }
                    $rating = $sum / $ratingsCount;
                }


                $purchasePrice += $price;
                $itemsPricesArr[$name] = $price;
                $itemsRatingsArr[$name] = $rating;
            }
        }
        else{
                $dataArr = array(
                    "status" => "error",
                );

                return new JsonResponse( $dataArr );
        }


        $dataArr = array(
            "status" => $status,
            "date" => $date,
            "purchasePrice" => $purchasePrice,
            "itemsCount" => $itemsCount,
            "itemsPricesArr" => $itemsPricesArr,
            "itemsRatingsArr" => $itemsRatingsArr,
        );

        return new JsonResponse( $dataArr );
    }



}
