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
        $isLogged = true;
        $hasRatings = false;
        $ratingValue = 0;
        $usersRating = 0;
        $user = $this->get('security.context')->getToken()->getUser();


        if($user == "anon.")
            $isLogged = false;


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
            $ratingValue = $this->getRating($itemDetails);
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
        $item = $em->getRepository('KTUShopBundle:Items')->findOneById($itemID);
        $itemDetails = $item->getItemsdetails();
        $value = $request->request->get('value');
        $usersRatings = $em->getRepository('KTUShopBundle:Ratings')->findByusers( $user );

        if( $usersRatings ){
            foreach(  $usersRatings as $rating ){
                if( $rating->getItemsdetails()->getId() == $itemDetails->getId() ){
                    $em->remove($rating);
                }
            }
        }

        $ratingObj = new Ratings();
        $ratingObj->setRating( $value );
        $ratingObj->setUsers( $user );
        $ratingObj->setItemsdetails( $itemDetails );

        $em->persist( $ratingObj );
        $em->flush();


        $ratingValue = $this->getRating($itemDetails);


        return new Response( $ratingValue );
    }

    /////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////


    private function hasRatings($itemDetails){
        $em = $this->getDoctrine()->getManager();
        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();

        $hasRatings = false;
        foreach( $ratingEntities as $rating ){
            if( $rating->getItemsdetails()->getId() == $itemDetails->getId() ){
                $hasRatings = true;
            }
        }

        return $hasRatings;
    }

    private function getRating($itemDetails){
        $em = $this->getDoctrine()->getManager();
        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();

        $ratingsSum = 0;
        $ratingsCount = 0;
        foreach( $ratingEntities as $rating ){
            if( $rating->getItemsdetails()->getId() == $itemDetails->getId() ){
                $ratingsSum = $ratingsSum + $rating->getRating();
                $ratingsCount = $ratingsCount + 1;
            }
        }

        $ratingValue = ($ratingsSum / $ratingsCount);

        return $ratingValue;
    }

    private function hasRated( $user, $itemDetails ){
        $em = $this->getDoctrine()->getManager();
        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();

        $hasRated = false;
        foreach( $ratingEntities as $rating ) {
            if ($rating->getUsers()->getId() == $user->getId() && $rating->getItemsdetails()->getId() == $itemDetails->getId()) {
                $hasRated = true;
            }
        }

        return $hasRated;
    }

    private function getUsersRating($user, $itemDetails){
        $em = $this->getDoctrine()->getManager();
        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();

        $usersRating = -1;
        foreach( $ratingEntities as $rating ) {
            if ($rating->getUsers()->getId() == $user->getId() && $rating->getItemsdetails()->getId() == $itemDetails->getId()) {
                $usersRating = $rating->getRating();
            }
        }

        return $usersRating;
    }

}
