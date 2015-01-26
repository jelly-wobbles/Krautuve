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
        $userEditor = $this->container->get('shop_user.editor');
        $isLogged = $userEditor->isLogged();

        if ( $isLogged ){
            $user = $securityContext->getToken()->getUser();
            $userID = $user->getId();
            $cartCount = $em->getRepository('KTUShopBundle:Shoppingcarts')->findUsersCartItemsCount($userID);
        }

        $itemsTotal = $em->getRepository('KTUShopBundle:Items')->findAvailableItemsCount();
        $pagesTotal = $this->getPagesTotal($itemsTotal, 6);
        $itemEntities = $em->getRepository('KTUShopBundle:Items')->findItemsByPage($page);
        $thumbnailEntities = $em->getRepository('KTUShopBundle:Images')->findByItemsArray($itemEntities);
        $categoryEntities = $em->getRepository('KTUShopBundle:Categories')->findThatHaveItems();
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
        $categoryEntities = $em->getRepository('KTUShopBundle:Categories')->findThatHaveItems();
        $itemsTotal = $em->getRepository('KTUShopBundle:Items')->findAvailableItemsCount($catObj);
        $pagesTotal = $this->getPagesTotal($itemsTotal, 6);

        $ratingEntities = $em->getRepository('KTUShopBundle:Ratings')->findAll();
        $thumbnailEntities = $em->getRepository('KTUShopBundle:Images')->findByItemsArray($itemEntities);


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



    private function getPagesTotal($itemsTotal, $pageSize){
        $pagesTotal = ceil( $itemsTotal / $pageSize );

        return $pagesTotal;
    }


}
