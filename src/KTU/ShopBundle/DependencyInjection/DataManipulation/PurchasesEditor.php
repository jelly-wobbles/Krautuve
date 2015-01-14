<?php


namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

use Doctrine\ORM\EntityManager;

class PurchasesEditor
{

    private $em;
    private $itemsEditor;

    public function __construct(EntityManager $em, $itemsEditor){
        $this->em = $em;
        $this->itemsEditor = $itemsEditor;
    }

    public function storageReportsHandler($data){

        $errors = null;

        foreach($data as $d){
            if(!$this->itemsEditor->checkStorage($d['item_id'], $d['quantity'], true)){
                array_push($errors, 'Prekės '.$d['item_name'].' '.$d['quantity'].'vnt. nėra sandėlyje.');
            }
            else{
                $this->itemsEditor->removeFromStorage($d['item_id'], $d['quantity']);
                $this->itemsEditor->addToStatus($d['item_id'], $d['quantity'], 4);
            }
        }

    }

    public function deliveryReportsHandler($data){

        $errors  = null;
        foreach($data as $d){
            $this->itemsEditor->addToStatus($d['item_id'], $d['quantity'], 3);
            $this->itemsEditor->removeFromStatus($d['item_id'], $d['quantity'], 4);
            // #TODO MARK as delivered
        }
    }

} 