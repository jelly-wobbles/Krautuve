<?php

namespace KTU\ShopBundle\Controller;

use KTU\ShopBundle\Entity\Images;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminItemsController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $itemsRep = $em->getRepository('KTUShopBundle:Itemsdetails');
        $items = $itemsRep->findAll();

        return $this->render('KTUShopBundle:Admin/items:items.html.twig', array(
            'items' => $items,
        ));
    }

    public function renderTableDataAction(Request $request)
    {
        if ($request->getMethod() == "POST") {

            $em = $this->getDoctrine()->getManager();
            $itemsRep = $em->getRepository('KTUShopBundle:Itemsdetails');

            $start = $request->request->get('start');
            $size = $request->request->get('size');

            $items = $itemsRep->findAll();
            $ratersCount = array();
            $rating = array();
            $quantity = array();

            foreach ($items as $item) {
                $id = $item->getId();
                $rating[$id] = $itemsRep->findItemRating($id);
                $ratersCount[$id] = $itemsRep->findItemRatersCount($id);
                $quantity[$id] = $itemsRep->findItemQuantity($id);
            }

            return $this->render('KTUShopBundle:Admin/items:itemsTableData.html.twig', array(
                'items' => $items,
                'rating' => $rating,
                'quantity' => $quantity,
                'ratersCount' => $ratersCount,
                'start' => $start,
                'size' => $size,
            ));
        }

        return new Response('false');
    }

    public function addItemAction(Request $request)
    {
        $form = $this->createForm($this->container->get('shop_item_add.form.type'));

        return $this->render('KTUShopBundle:Admin/items:addItem.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function editItemAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('KTUShopBundle:Itemsdetails')->find($id);
        $specs = $em->getRepository('KTUShopBundle:Specifications')->findByItemsdetails($item);
        $images = $em->getRepository('KTUShopBundle:Images')->findByItemsdetails($id);
        $specsVal = array();
        $specsNames = array();

        foreach($specs as $spec){
            array_push($specsVal, $spec->getValue());
            array_push($specsNames, $spec->getName());
        }

        $form = $this->createForm($this->container->get('shop_item_edit.form.type'));

        $itemName = $item->getName();

        $form->setData(array(
            'name' => $itemName,
            'category' => $item->getCategories(),
            'price' => $item->getPurePrice(),
            'specs_names' => $specsNames,
            'specs_values' => $specsVal,
            'description' => $item->getDescription(),
            'discount' => $item->getDiscount()
        ));

        return $this->render('KTUShopBundle:Admin/items:editItem.html.twig', array(
            'form' => $form->createView(),
            'images' => $images,
            'name' => $itemName,
            'id' => $id,
            'discount' => $item->getDiscount()*100,
            'discountedPrice' => $item->getPrice()
        ));
    }

    public function persistItemAction(Request $request)
    {
        $form = $this->createForm($this->container->get('shop_item_add.form.type'));
        $errorsJSON = null;
        $images = null;
        $sendErrors = false;

        if ($request->getMethod() == "POST") {
            $form->submit($request);

            if ($form->isValid()) {
                if (isset($request->files->get('shop_item_add')['uploaded'])) {
                    $images = array();
                    $errorsJSON['images'] = array();
                    $files = $request->files->get('shop_item_add')['uploaded'];
                    foreach ($files as $file) {
                        $img = new Images();
                        $img->setFile($file);

                        $errors = $this->get('validator')->validate($img, array('ImageFile'));

                        array_push($images, $img);

                        if (count($errors) != 0) {
                            $sendErrors = true;
                            foreach ($errors as $e) {
                                array_push($errorsJSON['images'], (string)$e->getMessage());
                            }
                        }
                        else{
                            array_push($errorsJSON['images'], null);
                        }
                    }

                }

                if($sendErrors){

                    $errorsJSON['name'] = false;
                    $errorsJSON['category'] = false;
                    $errorsJSON['price'] = false;
                    if(count($form->get('specs_names')) > 0){
                        $i = 0;
                        foreach($form->get('specs_names') as $name){
                            $errorsJSON['specs_names'][$i++] = false;
                        }
                    }
                    if(count($form->get('specs_values')) > 0){
                        $i = 0;
                        foreach($form->get('specs_values') as $name){
                            $errorsJSON['specs_values'][$i++] = false;
                        }
                    }
                    $errorsJSON['description'] = false;

                    return new JsonResponse(array(
                        'errors' => $errorsJSON,
                        'success' => false
                    ));
                }

                $itemEditor = $this->container->get('shop_items.editor');
                $newItem = $itemEditor->addItem($form->getData(), $images);

                return new JsonResponse(array(
                    'success' => true,
                    'redirectURL' => $this->get('router')->generate('admin_item_storage', array('id' => $newItem->getId()))
                ));
            }
            else{
                $errorsJSON['name'] = substr((string)$form->get('name')->getErrors(), 7);
                $errorsJSON['category'] = substr((string)$form->get('category')->getErrors(), 7);
                $errorsJSON['price'] = substr((string)$form->get('price')->getErrors(), 7);
                if(count($form->get('specs_names')) > 0){
                    $i = 0;
                    foreach($form->get('specs_names') as $name){
                        $errorsJSON['specs_names'][$i++] = substr((string)$name->getErrors(), 7);
                    }
                }
                if(count($form->get('specs_values')) > 0){
                    $i = 0;
                    foreach($form->get('specs_values') as $name){
                        $errorsJSON['specs_values'][$i++] = substr((string)$name->getErrors(), 7);
                    }
                }
                $errorsJSON['description'] = substr((string)$form->get('description')->getErrors(), 7);
                $errorsJSON['images'] = false;

                return new JsonResponse(array(
                    'errors' => $errorsJSON,
                    'success' => false
                ));

            }
        }

        return new JsonResponse(array(
            'success' => false
        ));

    }

    public function updateItemAction(Request $request, $id)
    {
        $form = $this->createForm($this->container->get('shop_item_edit.form.type'));
        $errorsJSON = null;
        $images = null;
        $sendErrors = false;

        if ($request->getMethod() == "POST") {
            $form->submit($request);

            if ($form->isValid()) {
                if (isset($request->files->get('shop_item_edit')['uploaded'])) {
                    $images = array();
                    $errorsJSON['images'] = array();
                    $files = $request->files->get('shop_item_edit')['uploaded'];
                    foreach ($files as $file) {
                        $img = new Images();
                        $img->setFile($file);

                        $errors = $this->get('validator')->validate($img, array('ImageFile'));

                        array_push($images, $img);

                        if (count($errors) != 0) {
                            $sendErrors = true;
                            foreach ($errors as $e) {
                                array_push($errorsJSON['images'], (string)$e->getMessage());
                            }
                        }
                        else{
                            array_push($errorsJSON['images'], null);
                        }
                    }

                }

                if(isset($request->request->get('shop_item_edit')['remove'])){
                    $removeImg = $request->request->get('shop_item_edit')['remove'];
                }
                else{
                    $removeImg = null;
                }

                if($sendErrors){

                    $errorsJSON['name'] = false;
                    $errorsJSON['category'] = false;
                    $errorsJSON['price'] = false;
                    if(count($form->get('specs_names')) > 0){
                        $i = 0;
                        foreach($form->get('specs_names') as $name){
                            $errorsJSON['specs_names'][$i++] = false;
                        }
                    }
                    if(count($form->get('specs_values')) > 0){
                        $i = 0;
                        foreach($form->get('specs_values') as $name){
                            $errorsJSON['specs_values'][$i++] = false;
                        }
                    }
                    $errorsJSON['description'] = false;

                    return new JsonResponse(array(
                        'errors' => $errorsJSON,
                        'success' => false
                    ));
                }

                $itemEditor = $this->container->get('shop_items.editor');
                $itemEditor->editItem($id, $form->getData(), $images, $removeImg);

                return new JsonResponse(array(
                    'success' => true,
                ));
            }
            else{
                $errorsJSON['name'] = substr((string)$form->get('name')->getErrors(), 7);
                $errorsJSON['category'] = substr((string)$form->get('category')->getErrors(), 7);
                $errorsJSON['price'] = substr((string)$form->get('price')->getErrors(), 7);
                if(count($form->get('specs_names')) > 0){
                    $i = 0;
                    foreach($form->get('specs_names') as $name){
                        $errorsJSON['specs_names'][$i++] = substr((string)$name->getErrors(), 7);
                    }
                }
                if(count($form->get('specs_values')) > 0){
                    $i = 0;
                    foreach($form->get('specs_values') as $name){
                        $errorsJSON['specs_values'][$i++] = substr((string)$name->getErrors(), 7);
                    }
                }
                $errorsJSON['description'] = substr((string)$form->get('description')->getErrors(), 7);
                $errorsJSON['images'] = false;

                return new JsonResponse(array(
                    'errors' => $errorsJSON,
                    'success' => false
                ));

            }
        }

        return new JsonResponse(array(
            'success' => false
        ));

    }

    public function removeItemAction(Request $request)
    {
        $itemEditor = $this->container->get('shop_items.editor');
        $itemEditor->removeItem($request->request->get('itemId'));

        return new Response('removed');
    }

    public function itemStorageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($this->container->get('shop_storage_edit.form.type'));

        $itemsDetailsRep = $em->getRepository('KTUShopBundle:Itemsdetails');
        $itemsDetails = $itemsDetailsRep->find($id);
        $stored = $itemsDetailsRep->findItemQuantity($id);
        $sent = $itemsDetailsRep->findItemSentQuantity($id);
        $sold = $itemsDetailsRep->findItemSoldQuantity($id);
        $delivered = $itemsDetailsRep->findItemQuantity($id, 3);

        return $this->render('KTUShopBundle:Admin/items:itemStorage.html.twig', array(
            'name' => $itemsDetails->getName(),
            'stored' => $stored,
            'sent' => $sent,
            'sold' => $sold,
            'form' => $form->createView(),
            'delivered' => $delivered,
            'id' => $id,
        ));
    }

    public function manageItemStorageAction(Request $request, $id, $intention)
    {
        $form = $this->createForm($this->container->get('shop_storage_edit.form.type'));

        if ($request->getMethod() == "POST") {
            $form->submit($request);

            $itemEditor = $this->container->get('shop_items.editor');

            if($intention == 'remove'){

                if($form->isValid()){

                    $count = $form->get('itemCount')->getData();
                    $storageAllowed = $itemEditor->checkStorage($id, $count, true);

                    if($storageAllowed === true){
                        $itemCount = $itemEditor->removeFromStorage($id, $count);

                        return new JsonResponse(array(
                            'success' => true,
                            'itemCount' => $itemCount
                        ));

                    }
                    else{
                        return new JsonResponse(array(
                            'errors' => $storageAllowed,
                            'success' => false
                        ));
                    }

                }

            }
            else if($intention == 'add'){

                if($form->isValid()){

                    $count = $form->get('itemCount')->getData();
                    $storageAllowed = $itemEditor->checkStorage($id, $count);

                    if($storageAllowed === true) {
                        $itemCount = $itemEditor->addToStorage($id, $form->get('itemCount')->getData());

                        return new JsonResponse(array(
                            'success' => true,
                            'itemCount' => $itemCount
                        ));
                    }
                    else{
                        return new JsonResponse(array(
                            'errors' => $storageAllowed,
                            'success' => false
                        ));
                    }
                }

            }

        }

        return new JsonResponse(array(
            'errors' => substr((string) $form->get('itemCount')->getErrors(), 7),
            'success' => false
        ));

    }

    public function itemDiscountAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($this->container->get('shop_discount_edit.form.type'));

        $itemsDetailsRep = $em->getRepository('KTUShopBundle:Itemsdetails');
        $itemsDetails = $itemsDetailsRep->find($id);

        return $this->render('KTUShopBundle:Admin/items:itemDiscount.html.twig', array(
            'itemDetails' => $itemsDetails,
            'form' => $form->createView()
        ));
    }

    public function manageItemDiscountAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($this->container->get('shop_discount_edit.form.type'));

        if ($request->getMethod() == "POST") {
            $form->submit($request);

            $itemEditor = $this->container->get('shop_items.editor');

            if($form->isValid()){

                $discount = $form->get('itemDiscount')->getData()/100;
                $item = $itemEditor->manageDiscount($id, $discount);

                return new JsonResponse(array(
                    'success' => true,
                    'prices' => array(
                        'lt' => number_format($item->getPriceLT(), 2,',',' '),
                        'e' => number_format($item->getPrice(), 2,',',' '),
                        'pure-lt' => number_format($item->getPurePriceLT(), 2,',',' '),
                        'pure-e' => number_format($item->getPurePrice(), 2,',',' '),
                    )
                ));
            }

        }

        return new JsonResponse(array(
            'errors' => substr((string) $form->get('itemDiscount')->getErrors(), 7),
            'success' => false
        ));

    }

    public function moreInfoAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $itemRepository = $em->getRepository('KTUShopBundle:Itemsdetails');
        $item = $itemRepository->find($id);
        $rating = $itemRepository->findItemRating($id);
        $sold = $itemRepository->findItemSoldQuantity($id);
        $stored = $itemRepository->findItemQuantity($id);
        $ratersCount = $itemRepository->findItemRatersCount($id);
        $specifications = $em->getRepository('KTUShopBundle:Specifications')->findByItemsdetails($item);

        return $this->render('KTUShopBundle:Admin/items:itemMoreInfo.html.twig', array(
            'item' => $item,
            'rating' => $rating,
            'sold' => $sold,
            'stored' => $stored,
            'ratersCount' => $ratersCount,
            'specifications' => $specifications
        ));
    }

}
