<?php

namespace KTU\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
Use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Response;
use KTU\ShopBundle\Entity\Ratings;

class itemReviewController extends Controller
{
    public function indexAction($itemID)
    {
        $em = $this->getDoctrine()->getManager();
        $cartCount = 0;
        $quantity = 0;
        $hasRated = false;
        $hasRatings = false;
        $ratingValue = 0;
        $usersRating = 0;
        $user = $this->get('security.context')->getToken()->getUser();
        $userEditor = $this->container->get('shop_user.editor');
        $isLogged = $userEditor->isLogged();


        $itemEntity = $em->getRepository('KTUShopBundle:Items')->findOneById($itemID);
        $itemDetails = $itemEntity->getItemsdetails();
        $imageEntities = $em->getRepository('KTUShopBundle:Images')->findByItemsdetails( $itemDetails );
        $specificationsArr = $em->getRepository('KTUShopBundle:Specifications')->findByItemsdetails( $itemDetails );

        if( sizeof($specificationsArr) == 0 ){
            $specificationsArr = null;
        }

        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();
        $allItems = $em->getRepository('KTUShopBundle:Items')->findAll();

        if( $isLogged ){
            $userID = $user->getId();

            if( $this->hasRated($user, $itemDetails) ){
                $hasRated = true;
                $usersRating = $this->getUsersRating($user, $itemDetails);
            }

            $cartCount = $cartCount = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartItemsCount($userID);
        }


        if( $this->hasRatings($itemDetails) ){
            $hasRatings = true;
            $ratingValue = $em->getRepository('KTUShopBundle:Ratings')->findItemsDetailsAverage( $itemDetails->getId() );
        }


        $quantity = $itemEntity->getQuantity();


        return $this->render('KTUShopBundle:itemReview:index.html.twig',
            array(
                  'itemEntity' => $itemEntity,
                  'itemDetails' => $itemDetails,
                  'imageEntities' => $imageEntities,
                  'cartCount' => $cartCount,
                  'ratingValue' => $ratingValue,
                  'quantity' => $quantity,
                  'hasRated' => $hasRated,
                  'hasRatings' => $hasRatings,
                  'specificationsArr' => $specificationsArr,
                  'usersRating' => $usersRating,
            ));

    }


    public function rateAction(Request $request, $itemID){

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $userID = $user->getId();
        $item = $em->getRepository('KTUShopBundle:Items')->findOneById($itemID);
        $itemDetails = $item->getItemsdetails();
        $value = $request->request->get('value');

        $ratingsEditor = $this->container->get('ratings.editor');
        $ratingsEditor->removeRating( $userID, $itemDetails->getId() );
        $ratingsEditor->addRating( $user, $itemDetails, $value );

        $ratingValue = $em->getRepository('KTUShopBundle:Ratings')->findItemsDetailsAverage( $itemDetails->getId() );


        return new Response( $ratingValue );
    }

    /////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////


    private function hasRatings($itemDetails){
        $ratingsEditor = $this->container->get('ratings.editor');
        $result = $ratingsEditor->hasRatings($itemDetails);
        return $result;
    }


    private function hasRated( $user, $itemDetails ){
        $ratingsEditor = $this->container->get('ratings.editor');
        $result = $ratingsEditor->hasRated( $user, $itemDetails);
        return $result;
    }

    private function getUsersRating($user, $itemDetails){
        $em = $this->getDoctrine()->getManager();
        $usersRating = $em->getRepository('KTUShopBundle:Ratings')->findUsersRating( $user, $itemDetails );

        return $usersRating;
    }

}
