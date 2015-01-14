<?php

namespace KTU\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('KTUShopBundle:Admin:main.html.twig');
    }

    // Ping to the server to check if the user is active
    public function activityCheckAction()
    {
        return new Response();
    }

    public function navigationBarStateAction($state)
    {
        $response = new Response();

        if($state == 'mini'){
            $cookie = new Cookie('admin_nav_bar_state', 'mini', $time = time() + (3600 * 24 * 60));

            $response->headers->setCookie($cookie);
            $response->send();
        }
        else{
            $response->headers->clearCookie('admin_nav_bar_state');
            $response->send();
        }

        return $response;
    }

}
