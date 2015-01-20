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
        $cartCount = NULL;

        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $securityContext->getToken()->getUser();
            $userID = $user->getId();
            $cartCount = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartItemsCount($userID);
        }

        $pagesTotal = $this->getPagesTotal();
        $itemEntities = $em->getRepository('KTUShopBundle:Items')->findItemsByPage($page);
        $thumbnailEntities = $this->getThumbnailsByItemsArray($itemEntities);
        $categoryEntities = $this->getCategoriesWithItems();

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
        $cartCount = NULL;

        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $securityContext->getToken()->getUser();
            $userID = $user->getId();
            $cartCount = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartItemsCount($userID);
        }

        $catObj = $em->getRepository('KTUShopBundle:Categories')->findOneByName( $category );


        $itemEntities = $em->getRepository('KTUShopBundle:Items')->findItemsByPage($page, $catObj);
        $categoryEntities = $this->getCategoriesWithItems();
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
        $user = $this->get('security.context')->getToken()->getUser();
        $userID = $user->getId();
        $itemID = $request->request->get('id');

        $cartEditor = $this->container->get('cart.editor');
        $response = $cartEditor->addToCart($userID, $itemID);

        return $response;
    }

    /////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////



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
