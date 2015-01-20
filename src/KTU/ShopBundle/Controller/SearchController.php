<?php

namespace KTU\ShopBundle\Controller;

use KTU\ShopBundle\Entity\Categories;
use KTU\ShopBundle\Entity\Shoppingcarts;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Response;
//Debug::dump($object);

class SearchController extends Controller
{
    public function indexAction($keyword, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $thumbnailEntities = array();
        $itemsTotal = 0;
        $pagesTotal = 0;
        $sliceFrom = ($page - 1) * 6 ;
        $cartCount = 0;
        $user = $this->get('security.context')->getToken()->getUser();
        $keyword = mb_convert_case($keyword, MB_CASE_LOWER, "UTF-8");

        $itemDetailsArr = $em->getRepository('KTUShopBundle:Itemsdetails')->findAll();


        $temp = array();
        foreach($itemDetailsArr as $itemDetails){
            $name = mb_convert_case($itemDetails->getName(), MB_CASE_LOWER, "UTF-8");
            $description = mb_convert_case($itemDetails->getDescription(), MB_CASE_LOWER, "UTF-8");

            if(
                (mb_strpos($name, $keyword,0,'UTF-8') !== false) ||
                (mb_strpos($description, $keyword,0,'UTF-8') !== false)
            ){
                array_push( $temp, $itemDetails );
            }
        }
        $itemDetailsArr = $temp;

        $itemEntities = array();

        foreach($itemDetailsArr as $itemDetails){
            $temp = $em->getRepository('KTUShopBundle:Items')->findByitemsdetails( $itemDetails );

            foreach( $temp as $tmp )
            array_push( $itemEntities, $tmp );
        }


        $temp = array();
        foreach($itemEntities as $item){
            if( $item->getItemstatuses()->getStatus() == "Available" ){
                array_push( $temp, $item );
            }
        }

        $itemEntities = $temp;

        $categoryEntities = $em->getRepository('KTUShopBundle:Categories')->findAll();
        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();
        $cartItems = $em->getRepository('KTUShopBundle:Shoppingcarts')->findByusers( $user );


        $temp = array();
        foreach( $categoryEntities as $category ){
            $arr = $em->getRepository('KTUShopBundle:Itemsdetails')->findBycategories( $category );

            if( $arr ){
                array_push( $temp, $category );
            }
        }
        $categoryEntities = $temp;


        $Detailsarr = array();
        $Itemsarr = array();
        foreach( $itemEntities as $item ){
            if( !( in_array($item->getItemsdetails()->getId(), $Detailsarr) ) ){
                array_push( $Detailsarr, $item->getItemsdetails()->getId() );
                array_push( $Itemsarr, $item );
            }
        }

        $itemEntities = $Itemsarr;


        if( $cartItems )
        {
            $cartCount = sizeof( $cartItems );
        }


        foreach ($itemEntities as $item){
            $detailsObj = $item->getItemsdetails();
            $imageObj = $em->getRepository('KTUShopBundle:Images')->findByitemsdetails( $detailsObj );

            if($imageObj){
                $imageObj = array_pop($imageObj);
                array_push( $thumbnailEntities, $imageObj );
            }
        }

        $itemsTotal = sizeof( $itemEntities );
        $pagesTotal = ceil( $itemsTotal / 6 );


        $itemEntities = array_slice( $itemEntities, $sliceFrom, 6);

        return $this->render('KTUShopBundle:Main:index.html.twig',
            array('itemEntities' => $itemEntities,
                'categoryEntities' => $categoryEntities,
                'thumbnailEntities' => $thumbnailEntities,
                'itemsTotal' => $itemsTotal,
                'pagesTotal' => $pagesTotal,
                'currentPage' => $page,
                'keyword' => $keyword,
                'category' => null,
                'cartCount' => $cartCount,
                'ratingEntities' => $ratingEntities,
            ));
    }




}
