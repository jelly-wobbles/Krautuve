<?php

namespace KTU\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
Use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Response;
//Debug::dump($object);

class baseViewController extends Controller
{
    public function indexAction()
    {

        return $this->render('KTUShopBundle:baseView:index.html.twig');
    }


}
