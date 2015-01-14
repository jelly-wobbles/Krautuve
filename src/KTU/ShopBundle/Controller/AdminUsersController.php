<?php

namespace KTU\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminUsersController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('KTUShopBundle:Users')->findAll();

        return $this->render('KTUShopBundle:Admin/users:users.html.twig', array('users' => $users));
    }

    public function renderTableDataAction(Request $request)
    {
        if ($request->getMethod() == "POST") {

            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository('KTUShopBundle:Users')->findAll();

            $start = $request->request->get('start');
            $size = $request->request->get('size');

            return $this->render('KTUShopBundle:Admin/users:userTableData.html.twig', array(
                'users' => $users,
                'start' => $start,
                'size' => $size,
            ));
        }

        return new Response('false');
    }

    public function userEditAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userEditor = $this->container->get('shop_user.editor');
        $form = $this->createForm($this->container->get('shop_user_edit.form.type'));
        $user = $em->getRepository('KTUShopBundle:Users')->find($id);

        // Check for post data
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            //if form is valid put the data to database
            if($form->isValid()){
                $userEditor->edit($id ,$request->request->all()['shop_user_edit']);

                $url = $this->container->get('router')->generate('admin_users');
                return new RedirectResponse($url);
            }
        }
        else{
            $form->setData($user);
        }

        return $this->render('KTUShopBundle:Admin/users:userEdit.html.twig', array('form' => $form->createView(), 'user' => $user));
    }

    public function moreInfoAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('KTUShopBundle:Users')->find($id);

        return $this->render('KTUShopBundle:Admin/users:userMoreInfo.html.twig', array(
            'user' => $user
        ));

    }

    public function removeUserAction(Request $request){

        $email = $request->request->get('userEmail');
        $userEditor = $this->container->get('shop_user.editor');

        $userEditor->remove($email);

        return new Response($email." deleted.");
    }

    public function banUserAction(Request $request)
    {

        $email = $request->request->get('userEmail');
        $userEditor = $this->container->get('shop_user.editor');

        $userEditor->ban($email);

        return new Response($email." blocked.");
    }

    public function unbanUserAction(Request $request)
    {

        $email = $request->request->get('userEmail');
        $userEditor = $this->container->get('shop_user.editor');

        $userEditor->unban($email);

        return new Response($email . " unblocked.");
    }

    public function activateUserAction(Request $request)
    {
        $email = $request->request->get('userEmail');
        $userEditor = $this->container->get('shop_user.editor');

        $userEditor->activate($email);

        return new Response("activated.");
    }
}
