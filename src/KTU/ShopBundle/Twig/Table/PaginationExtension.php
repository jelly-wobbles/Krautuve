<?php

namespace KTU\ShopBundle\Twig\Table;


class PaginationExtension extends \Twig_Extension
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'tablePagination'  => new \Twig_Function_Method($this, 'tablePagination', array('is_safe' => array('html'))),
        );
    }

    public function tablePagination($objectCount=0, $objectsPerPage=10)
    {

        $templating = $this->container->get('templating');

        return $templating->render('KTUShopBundle:Table:pagination.html.twig',
            array(
                'objectCount' => $objectCount,
                'objectsPerPage' => $objectsPerPage,
                'totalPages' => $this->totalPages($objectCount, $objectsPerPage)
            )
        );

    }

    private function totalPages($objectCount, $objectsPerPage)
    {
        if($objectCount == 0 || $objectCount < $objectsPerPage){
            return 1;
        }

        return ceil($objectCount / $objectsPerPage);
    }

    public function getName()
    {
        return('tablePagination');
    }

} 