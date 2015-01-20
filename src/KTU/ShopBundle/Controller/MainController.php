<?php

namespace KTU\ShopBundle\Controller;

use KTU\ShopBundle\Entity\Shoppingcarts;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Response;
//Debug::dump($object);

class MainController extends Controller
{
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();


        $pagesTotal = $this->getPagesTotal();
        $itemEntities = $this->getItemsByPage($page);
        $thumbnailEntities = $this->getThumbnailsByItemsArray($itemEntities);
        $categoryEntities = $this->getCategoriesWithItems();
        $cartCount = $this->getUsersCartAmount($user);
        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();


        return $this->render('KTUShopBundle:Main:index.html.twig',
            array('itemEntities' => $itemEntities,
                  'categoryEntities' => $categoryEntities,
                  'thumbnailEntities' => $thumbnailEntities,
                  'pagesTotal' => $pagesTotal,
                  'currentPage' => $page,
                  'category' => null,
                  'cartCount' => $cartCount,
                  'ratingEntities' => $ratingEntities,
            ));
    }



    public function loadByCategoryAction($category, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $catObj = $em->getRepository('KTUShopBundle:Categories')->findOneByName( $category );

        $itemEntities = $this->getItemsByPage($page, $catObj);
        $categoryEntities = $this->getCategoriesWithItems();
        $cartCount = $this->getUsersCartAmount($user);
        $pagesTotal = $this->getPagesTotal($catObj);
        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();
        $thumbnailEntities = $this->getThumbnailsByItemsArray( $itemEntities );


        return $this->render('KTUShopBundle:Main:index.html.twig',
            array(
                'itemEntities' => $itemEntities,
                'categoryEntities' => $categoryEntities,
                'thumbnailEntities' => $thumbnailEntities,
                'pagesTotal' => $pagesTotal,
                'currentPage' => $page,
                'category' => $catObj,
                'cartCount' => $cartCount,
                'ratingEntities' => $ratingEntities,
            ));
    }



    public function toCartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $idItem = $request->request->get('id');
        $item = $em->getRepository('KTUShopBundle:Items')->find( $idItem );

        if( !($item) ){
            return new Response( -1 );
        }

        $shopAmount = $item->getQuantity();
        $cartAmount = 0;
        $amount = 0;

        $user = $em->getRepository('KTUShopBundle:Users')->findOneByid( $user->getId() );

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

        $amount = $this->getUsersCartAmount($user);

        return new Response( $amount );
    }

    /////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////

    private function getItemsByPage($page, $category = NULL){
        $em = $this->getDoctrine()->getManager();

        $sliceFrom = ($page - 1) * 6 ;

        if( $category == NULL ){
            $itemEntities = $em->getRepository('KTUShopBundle:Items')->findByitemstatuses(1);
        }
        else{
            $itemDetails = $em->getRepository('KTUShopBundle:Itemsdetails')->findBycategories( $category );
            $itemEntities = $em->getRepository('KTUShopBundle:Items')->findByitemsdetails( $itemDetails );
            $Detailsarr = array();
            $Itemsarr = array();
            foreach( $itemEntities as $item ){
                if( !( in_array($item->getItemsdetails()->getId(), $Detailsarr) ) ){
                    array_push( $Detailsarr, $item->getItemsdetails()->getId() );
                    array_push( $Itemsarr, $item );
                }
            }

            $itemEntities = $Itemsarr;
        }


        $temp = array();
        foreach($itemEntities as $item){
            $statusID = $item->getItemstatuses()->getId();
            $itemQuantity = $item->getQuantity();

            if( ( $statusID == 1 && $itemQuantity > 0 ) ){
                array_push( $temp, $item );
            }
        }
        $itemEntities = $temp;

        $itemEntities = array_slice( $itemEntities, $sliceFrom, 6);

        return $itemEntities;
    }

    private function getThumbnailsByItemsArray($itemEntities){

        $em = $this->getDoctrine()->getManager();
        $imageEntities = array();

        foreach ($itemEntities as $item){
            $detailsObj = $item->getItemsdetails();
            $imageObj = $em->getRepository('KTUShopBundle:Images')->findByitemsdetails( $detailsObj );

            if($imageObj){
                $imageObj = array_pop($imageObj);
                array_push( $imageEntities, $imageObj );
            }
        }

        return $imageEntities;
    }

    private function getCategoriesWithItems(){
        $em = $this->getDoctrine()->getManager();

        $categoryEntities = $em->getRepository('KTUShopBundle:Categories')->findAll();
        $temp = array();
        foreach( $categoryEntities as $category ){
            $arr = $em->getRepository('KTUShopBundle:Itemsdetails')->findBycategories( $category );

            if( $arr ){
                array_push( $temp, $category );
            }
        }
        $categoryEntities = $temp;

        return $categoryEntities;
    }

    private function getUsersCartAmount($user){
        $em = $this->getDoctrine()->getManager();

        $cartItemsRepository = $em->getRepository('KTUShopBundle:Shoppingcarts');

        $userID = $user->getId();
        $cartCount = $cartItemsRepository->findUsersCartItemsCount($userID);

        return $cartCount;
    }


    private function getPagesTotal($category = NULL){
        $em = $this->getDoctrine()->getManager();

        if( $category == NULL ){
            $allItemEntities = $em->getRepository('KTUShopBundle:Items')->findByitemstatuses(1);
            $itemsTotal = sizeof( $allItemEntities );
            $pagesTotal = ceil( $itemsTotal / 6 );
        }
        else{
            $itemDetails = $em->getRepository('KTUShopBundle:Itemsdetails')->findBycategories( $category );
            $allItemEntities = $em->getRepository('KTUShopBundle:Items')->findByitemsdetails( $itemDetails );
            $Detailsarr = array();
            $Itemsarr = array();
            foreach( $allItemEntities as $item ){
                if( !( in_array($item->getItemsdetails()->getId(), $Detailsarr) ) ){
                    array_push( $Detailsarr, $item->getItemsdetails()->getId() );
                    array_push( $Itemsarr, $item );
                }
            }
            $allItemEntities = $Itemsarr;
            $itemsTotal = sizeof( $allItemEntities );
            $pagesTotal = ceil( $itemsTotal / 6 );
        }


        return $pagesTotal;
    }


}
