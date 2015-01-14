<?php

namespace KTU\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class AdminCategoriesController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categoriesRepository = $em->getRepository('KTUShopBundle:Categories');
        $categories = $categoriesRepository->findAll();

        return $this->render('KTUShopBundle:Admin/categories:categories.html.twig', array(
            'categories' => $categories,
            )
        );
    }

    public function renderTableDataAction(Request $request)
    {
        if ($request->getMethod() == "POST") {
            $em = $this->getDoctrine()->getManager();
            $categoriesRepository = $em->getRepository('KTUShopBundle:Categories');

            $start = $request->request->get('start');
            $size = $request->request->get('size');

            $categories = $categoriesRepository->findAll();
            $itemCount = $categoriesRepository->findItemCount();

            return $this->render('KTUShopBundle:Admin/categories:categoriesTableData.html.twig', array(
                'categories' => $categories,
                'itemCount' => $itemCount,
                'start' => $start,
                'size' => $size
            ));

        }

        return new Response('false');

    }

    public function addCategoryAction(Request $request)
    {

        $form = $this->createForm($this->container->get('shop_categories_add.form.type'));

        if ($request->getMethod() == "POST") {
            $form->submit($request);
            //if form is valid put the data to database
            if($form->isValid()){

                $categeoriesEditor = $this->container->get('shop_categories.editor');
                $categeoriesEditor->addCategory($form->get('name')->getData());

                $url = $this->generateUrl('admin_categories');
                return new RedirectResponse($url);
            }
        }

        return $this->render('KTUShopBundle:Admin/categories:addCategory.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function removeCategoryAction(Request $request)
    {

        $id = $request->request->get('categoryId');
        $categoryEditor = $this->container->get('shop_categories.editor');

        $categoryEditor->removeCategory($id);

        return new JsonResponse(array(
            'success' => true
        ));

    }
}
