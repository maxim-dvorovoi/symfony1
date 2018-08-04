<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends Controller
{
    /**
     * @Route("/tags/{id}", name="tags_item")
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

        $tags = $this->getDoctrine()->getRepository('AppBundle:Tags')->find($id);
        if (!$tags) {
            throw $this->createNotFoundException('Category not found');
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT p FROM AppBundle:Post p WHERE p.tags=$id ORDER BY p.id DESC";
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
                return $this->redirectToRoute('tags_item', ['id' => $id ]);
            }
        }

        return [
            'tags' => $tags,
            'pagination' => $pagination

        ];
    }

    /**
     * @Route("/user/tags/{id}", name="login_tags_item")
     */
    public function showloginAction($id,Request $request)
    {
        $tags = $this->getDoctrine()->getRepository('AppBundle:Tags')->find($id);
        if (!$tags) {
            throw $this->createNotFoundException('Category not found');
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT p FROM AppBundle:Post p WHERE p.tags=$id ORDER BY p.id DESC";
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
                return $this->redirectToRoute('login_tags_item',['id' => $id]);
            }
        }

        return $this->render('@App/login/tags/show.html.twig',[
            'tags' => $tags,
            'pagination' => $pagination

        ]);
    }

    /**
     * @Route("/admins/tags/{id}", name="admin_tags_item")
     */
    public function showAdminAction($id,Request $request)
    {
        $tags = $this->getDoctrine()->getRepository('AppBundle:Tags')->find($id);
        if (!$tags) {
            throw $this->createNotFoundException('Category not found');
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT p FROM AppBundle:Post p WHERE p.tags=$id ORDER BY p.id DESC";
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
                return $this->redirectToRoute('admin_tags_item',['id' => $id]);
            }
        }

        return $this->render('@App/admin/tags/show.html.twig',[
            'tags' => $tags,
            'pagination' => $pagination

        ]);
    }

}