<?php

namespace KTU\ShopBundle\DependencyInjection\DataManipulation;

class ReadFilter
{
    private $objPHPExcel;

    public function __construct($file=null)
    {
        if($file != null){
            $this->init($file);
        }
    }

    public function init($file)
    {
        $inputFileType = \PHPExcel_IOFactory::identify($file);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $this->objPHPExcel = $objReader->load($file);
    }

    // Delivery report template
    public function readDeliveryReport()
    {
        $data = array();
        //  Get worksheet dimensions
        $sheet = $this->objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //  Loop through each row of the worksheet in turn
        for ($row = 3; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);
            //  Store data into temp object
            $temp['nr'] = $rowData[0][0];
            $temp['purchase_id'] = $rowData[0][1];
            $temp['item_id'] = $rowData[0][2];
            $temp['item_name'] = $rowData[0][3];
            $temp['quantity'] = $rowData[0][4];
            $temp['user_email'] = $rowData[0][5];
            array_push($data, $temp);
        }
        return $data;
    }

    // Storage report template
    public function readStorageReport()
    {
        $data = array();
        //  Get worksheet dimensions
        $sheet = $this->objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //  Loop through each row of the worksheet in turn
        for ($row = 3; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                NULL,
                TRUE,
                FALSE);
            //  Store data into temp object
            $temp['nr'] = $rowData[0][0];
            $temp['item_id'] = $rowData[0][1];
            $temp['item_name'] = $rowData[0][2];
            $temp['quantity'] = $rowData[0][3];
            array_push($data, $temp);
        }
        return $data;
    }
}