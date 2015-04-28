<?php

namespace KTU\ShopBundle\Table;

class TableBase
{
    private $paginator;
    private $request;
    private $entityManager;

    public $list = null;
    public $itemsPerPage = 10;
    public $ippOptions = array(10, 25, 50);
    public $sortDirection = null;
    public $sortProperty = null;
    public $filter = null;
    public $availabeFilter = array();
    public $searchfields = array();
    public $currentPage = 1;

    public function __construct($paginator, $requestStack, $em){
        $this->paginator = $paginator;
        $this->request = $requestStack;
        $this->entityManager = $em;
    }

    public function tableJSON()
    {
        $tableInfo = array();

        $data = $this->paginator->paginate(
            $this->list,
            $this->currentPage
        );

        return json_encode($data);

    }

    public function setTableCookies(){

    }

    public function getTableParameter($name)
    {
        return 'something';
    }

}