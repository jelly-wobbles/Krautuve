<?php

namespace KTU\ShopBundle\Controller;

use KTU\ShopBundle\DependencyInjection\DataManipulation\ReadFilter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPurchasesController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $purchasesRep = $em->getRepository('KTUShopBundle:Purchases');
        $purchases = $purchasesRep->findAll();

        return $this->render('KTUShopBundle:Admin/purchases:purchases.html.twig', array(
            'purchases' => $purchases
        ));
    }

    public function renderTableDataAction(Request $request)
    {

       // if ($request->getMethod() == "POST") {
            $em = $this->getDoctrine()->getManager();
            $purchasesRep = $em->getRepository('KTUShopBundle:Purchases');
            $start = $request->request->get('start');
            $size = $request->request->get('size');

            $purchases = $purchasesRep->findAll();
            $itemCounts = array();
            $purchasesSums = array();

            foreach($purchases as $p){
                $id = $p->getId();
                $itemCounts[$id] = $purchasesRep->findItemQuantity($id);
                $purchasesSums[$id] = $purchasesRep->findPurchaseSum($id);
            }

            return $this->render('KTUShopBundle:Admin/purchases:purchasesTableData.html.twig', array(
                'purchases' => $purchases,
                'itemCounts' => $itemCounts,
                'purchasesSums' => $purchasesSums,
                'start' => $start,
                'size' => $size
            ));

       // }

       return new Response('false');

    }

    public function purchasesMoreInfoAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $purchaseRep = $em->getRepository('KTUShopBundle:Purchases');
        $purchase = $purchaseRep->find($id);
        $purchaseItems = $em->getRepository('KTUShopBundle:Itemspurchases')->findByPurchases($purchase);
        $purchaseSum = $purchaseRep->findPurchaseSum($id);

        return $this->render('KTUShopBundle:Admin/purchases:purchaseMoreInfo.html.twig',
            array(
                'purchase' => $purchase,
                'purchaseItems' => $purchaseItems,
                'purchaseSum' => $purchaseSum
            )
        );
    }

    public function purchasesReportsAction(Request $request)
    {
        $form = $this->createForm($this->container->get('shop_purchases.report.form.type'));

        return $this->render('KTUShopBundle:Admin/purchases:purchaseReports.html.twig',
            array(
             'form' => $form->createView()
            )
        );
    }

    public function purchasesStorageReportAction(Request $request)
    {
        $form = $this->createForm($this->container->get('shop_purchases.report.form.type'));

         if($request->getMethod() == 'POST'){
              $form->submit($request);
              if($form->isValid()){
                  $files = $request->files->get('attachment');
                  $reader = new ReadFilter();

                  $dataArray = array();

                  foreach($files as $f){
                      $reader->init($f);
                      array_push($dataArray, $reader->readStorageReport($f->getRealPath()));
                  }

                  $FormAndErrors = $this->createFormAndSetDataStorage($dataArray);

                  $form = $this->createForm($this->container->get('shop_purchases.report.storage.form.type'));
                  $form->setData(array(
                      'storageReportJSON' => json_encode($FormAndErrors['validRowsJSON']),
                  ));

                  return $this->render('KTUShopBundle:Admin/purchases:storageReport.html.twig',
                      array(
                          'files' => $dataArray,
                          'errors' => $FormAndErrors['errors'],
                          'form' => $form->createView()
                      )
                  );
              }
          }

        return new Response('false');
    }

    public function purchasesDeliveryReportAction(Request $request)
    {
        $form = $this->createForm($this->container->get('shop_purchases.report.form.type'));

        if($request->getMethod() == 'POST'){
            $form->submit($request);
            if($form->isValid()){
                $files = $request->files->get('attachment');
                $reader = new ReadFilter();

                $dataArray = array();

                foreach($files as $f){
                    $reader->init($f);
                    array_push($dataArray, $reader->readDeliveryReport($f->getRealPath()));
                }

                $FormAndErrors = $this->createFormAndSetDataDelivery($dataArray);

                $form = $this->createForm($this->container->get('shop_purchases.report.delivery.form.type'));
                $form->setData(array(
                    'deliveryReportJSON' => json_encode($FormAndErrors['validRowsJSON']),
                ));

                return $this->render('KTUShopBundle:Admin/purchases:deliveryReport.html.twig',
                    array(
                        'files' => $dataArray,
                        'errors' => $FormAndErrors['errors'],
                        'form' => $form->createView()
                    )
                );
            }
        }

        return new Response('false');
    }


    public function purchasesDeliveryReportSubmitAction(Request $request)
    {

        $form = $this->createForm($this->container->get('shop_purchases.report.delivery.form.type'));

        if($request->getMethod() == 'POST'){
            $form->submit($request);
            if($form->isValid()){
                $data = json_decode($form->get('deliveryReportJSON')->getData(), true);
                $purchasesEditor = $this->container->get('shop_purchases.editor');

                $purchasesEditor->deliveryReportsHandler($data);

                $url = $this->generateUrl('admin_items');
                return new RedirectResponse($url);
            }
        }

        return new JsonResponse(array(
            'success' => 'false',
        ));
    }

    public function purchasesStorageReportSubmitAction(Request $request)
    {
        $form = $this->createForm($this->container->get('shop_purchases.report.storage.form.type'));

        if($request->getMethod() == 'POST'){
            $form->submit($request);
            if($form->isValid()){
                $data = json_decode($form->get('storageReportJSON')->getData(), true);
                $purchasesEditor = $this->container->get('shop_purchases.editor');

                $purchasesEditor->storageReportsHandler($data);

                $url = $this->generateUrl('admin_items');
                return new RedirectResponse($url);
            }
        }

        return new JsonResponse(array(
            'success' => 'false',
        ));
    }

    private function createFormAndSetDataStorage($data)
    {
        // error = 1 - id not found
        // error = 2 - name mismatch
        // error = 3 - invalid quantity
        // [] - file , [] - row
        $errorRows = [];
        $validRowsJSON = [];
        $em = $this->getDoctrine()->getManager();
        $itemDetailsRep = $em->getRepository('KTUShopBundle:Itemsdetails');

        $fileIndex = 0;
        foreach($data as $file){
            foreach($file as $row){
                $item = $itemDetailsRep->find($row['item_id']);
                if($item != null){
                    $itemName = $item->getName();
                    $itemQuantity = $itemDetailsRep->findItemQuantity($row['item_id'],1);
                    if(!is_numeric($row['quantity'])){
                        if(!is_int($row['quantity'])){
                            $errorRows[$fileIndex][$row['nr']-1] = 3;
                        }
                    }
                    if($itemQuantity < $row['quantity']){
                        $errorRows[$fileIndex][$row['nr']-1] = 3;
                    }
                    if(strcasecmp($itemName, $row['item_name']) == 1){
                        $errorRows[$fileIndex][$row['nr']-1] = 2;
                    }
                    else{
                        if(!isset($errorRows[$fileIndex][$row['nr']-1])){
                            $errorRows[$fileIndex][$row['nr']-1] = 0;

                            $temp['item_id'] = $row['item_id'];
                            $temp['quantity'] = (int)$row['quantity'];

                            array_push($validRowsJSON, $temp);
                        }
                    }
                }
                else{
                    $errorRows[$fileIndex][$row['nr']-1] = 1;
                }
            }

            $fileIndex++;
        }

        return array(
            'errors' => $errorRows,
            'validRowsJSON' => $validRowsJSON
        );
    }

    private function createFormAndSetDataDelivery($data)
    {
        // error = 1 - id not found or email is not found or purchase if invalid
        // error = 2 - name mismatch
        // error = 3 - invalid quantity
        // [] - file , [] - row
        $errorRows = [];
        $validRowsJSON = [];
        $em = $this->getDoctrine()->getManager();
        $itemDetailsRep = $em->getRepository('KTUShopBundle:Itemsdetails');

        $fileIndex = 0;
        foreach($data as $file){
            foreach($file as $row){

                $user = $em->getRepository('KTUShopBundle:Users')->findOneByEmail($row['user_email']);

                $item = $itemDetailsRep->find($row['item_id']);
                if($item != null){
                    $itemName = $item->getName();
                    $purchase = $em->getRepository('KTUShopBundle:Purchases')->find($row['purchase_id']);
                    $itemQuantity = -1;
                    if($purchase != null){
                        $itemPur = $em->getRepository('KTUShopBundle:Itemspurchases')->findByPurchases($purchase);

                        foreach($itemPur as $ip){
                            if($ip->getItems()->getItemsdetails()->getId() == $row['item_id']){
                                $itemQuantity = $ip->getQuantity();
                            }
                        }

                        if(!is_numeric($row['quantity'])){
                            if(!is_int($row['quantity'])){
                                $errorRows[$fileIndex][$row['nr']-1] = 3;
                            }
                        }
                        if($itemQuantity != $row['quantity']){
                            $errorRows[$fileIndex][$row['nr']-1] = 3;
                        }
                        if(strcasecmp($itemName, $row['item_name']) == 1){
                            $errorRows[$fileIndex][$row['nr']-1] = 2;
                        }
                    }
                    else{
                        $errorRows[$fileIndex][$row['nr']-1] = 1;
                    }

                }
                else{
                    $errorRows[$fileIndex][$row['nr']-1] = 1;
                }

                if($user != null){
                    if(!isset($errorRows[$fileIndex][$row['nr']-1])){
                        $errorRows[$fileIndex][$row['nr']-1] = 0;

                        $temp['purchase_id'] = $row['purchase_id'];
                        $temp['user_email'] = $row['user_email'];
                        $temp['item_id'] = $row['item_id'];
                        $temp['quantity'] = (int)$row['quantity'];

                        array_push($validRowsJSON, $temp);
                    }
                }
                else{
                    $errorRows[$fileIndex][$row['nr']-1] = 1;
                }

            }

            $fileIndex++;
        }

        return array(
            'errors' => $errorRows,
            'validRowsJSON' => $validRowsJSON
        );
    }

}
