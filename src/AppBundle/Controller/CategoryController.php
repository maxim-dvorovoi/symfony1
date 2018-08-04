<?php


namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{id}", name="category_item")
     * @Template()
     */
    public function showAction($id,Request $request)
    {
        $auth = $this->getUser();
        if ($auth){
            $auth = $auth->getRoles();
            if ($auth[0] == 'ROLE_USER'){
                return $this->redirectToRoute('login_homepage');
            }
        }

        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT p FROM AppBundle:Post p WHERE p.category=$id AND p.tags!=3  ORDER BY p.id DESC";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        if (isset($_POST['submit'])) {
            if ($_POST['search'] == 'Политика') {
                return $this->redirectToRoute('category_item', ['id' => 1]);
            } elseif ($_POST['search'] == 'Экономика') {
                return $this->redirectToRoute('category_item', ['id' => 4]);
            } elseif ($_POST['search'] == 'Финансовые новости') {
                return $this->redirectToRoute('category_item', ['id' => 2]);
            } elseif ($_POST['search'] == 'Финансовые прогнозы') {
                return $this->redirectToRoute('category_item', ['id' => 3]);
            } elseif ($_POST['search'] == 'Топ') {
                return $this->redirectToRoute('tags_item', ['id' => 2]);
            } elseif ($_POST['search'] == 'Круто') {
                return $this->redirectToRoute('tags_item', ['id' => 1]);
            } else {
                return $this->redirectToRoute('category_item', ['id' => $id]);
            }
        }

        return [
            'category' => $category,
            'pagination' => $pagination

        ];
    }

    /**
     * @Route("/user/category/{id}", name="login_category_item")
     */
    public function loginAction($id,Request $request)
    {
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT p FROM AppBundle:Post p WHERE p.category=$id AND p.tags!=3 ORDER BY p.id DESC";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        if (isset($_POST['submit'])) {
            if ($_POST['search'] == 'Политика') {
                return $this->redirectToRoute('login_category_item', ['id' => 1]);
            } elseif ($_POST['search'] == 'Экономика') {
                return $this->redirectToRoute('login_category_item', ['id' => 4]);
            } elseif ($_POST['search'] == 'Финансовые новости') {
                return $this->redirectToRoute('login_category_item', ['id' => 2]);
            } elseif ($_POST['search'] == 'Финансовые прогнозы') {
                return $this->redirectToRoute('login_category_item', ['id' => 3]);
            } elseif ($_POST['search'] == 'Аналитика') {
                return $this->redirectToRoute('login_tags_item', ['id' => 3]);
            } elseif ($_POST['search'] == 'Топ') {
                return $this->redirectToRoute('login_tags_item', ['id' => 2]);
            } elseif ($_POST['search'] == 'Круто') {
                return $this->redirectToRoute('login_tags_item', ['id' => 1]);
            } else {
                return $this->redirectToRoute('login_category_item', ['id' => $id]);
            }
        }

        return $this->render('@App/login/category/show.html.twig',[
            'category' => $category,
            'pagination' => $pagination

        ]);
    }

    /**
     * @Route("/admins/category/{id}", name="admin_category_item")
     */
    public function adminAction($id,Request $request)
    {
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT p FROM AppBundle:Post p WHERE p.category=$id AND p.tags!=3 ORDER BY p.id DESC";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        if (isset($_POST['submit'])) {
            if ($_POST['search'] == 'Политика') {
                return $this->redirectToRoute('admin_category_item', ['id' => 1]);
            } elseif ($_POST['search'] == 'Экономика') {
                return $this->redirectToRoute('admin_category_item', ['id' => 4]);
            } elseif ($_POST['search'] == 'Финансовые новости') {
                return $this->redirectToRoute('admin_category_item', ['id' => 2]);
            } elseif ($_POST['search'] == 'Финансовые прогнозы') {
                return $this->redirectToRoute('admin_category_item', ['id' => 3]);
            } elseif ($_POST['search'] == 'Аналитика') {
                return $this->redirectToRoute('admin_tags_item', ['id' => 3]);
            } elseif ($_POST['search'] == 'Топ') {
                return $this->redirectToRoute('admin_tags_item', ['id' => 2]);
            } elseif ($_POST['search'] == 'Круто') {
                return $this->redirectToRoute('admin_tags_item', ['id' => 1]);
            } else {
                return $this->redirectToRoute('admin_category_item', ['id' => $id]);
            }
        }

        return $this->render('@App/admin/category/show.html.twig',[
            'category' => $category,
            'pagination' => $pagination

        ]);
    }

}