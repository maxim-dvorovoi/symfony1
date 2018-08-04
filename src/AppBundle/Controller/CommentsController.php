<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends Controller
{
    /**
     * @Route("/comments/{id}", name="comments_page")
     * @Template()
     */
    public function indexAction(Request $request, $id)
    {
        $auth = $this->getUser();
        if ($auth){
            $auth = $auth->getRoles();
            if ($auth[0] == 'ROLE_USER'){
                return $this->redirectToRoute('login_homepage');
            }
        }

        $comments = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['user' => $id]);

        if (!$comments) {
            throw $this->createNotFoundException('Comments not found');
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT c FROM AppBundle:Comments c WHERE c.user = $id ORDER BY c.id DESC";
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
                return $this->redirectToRoute('comments_page', ['id' => $id]);
            }
        }

        return [
            'comments' => $comments,
            'pagination' => $pagination
        ];
    }

    /**
     * @Route("/user/comments/{id}", name="login_comments_page")
     */
    public function loginindexAction(Request $request, $id)
    {
        $comments = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['user' => $id]);

        if (!$comments) {
            throw $this->createNotFoundException('Comments not found');
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT c FROM AppBundle:Comments c WHERE c.user = $id ORDER BY c.id DESC";
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
                return $this->redirectToRoute('login_comments_page',['id' => $id]);
            }
        }

        return $this->render('@App/login/comments/index.html.twig', [
            'comments' => $comments,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admins/comments/{id}", name="admin_comments_page")
     */
    public function adminindexAction(Request $request, $id)
    {
        $comments = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['user' => $id]);

        if (!$comments) {
            throw $this->createNotFoundException('Comments not found');
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT c FROM AppBundle:Comments c WHERE c.user = $id ORDER BY c.id DESC";
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
                return $this->redirectToRoute('admin_comments_page',['id' => $id]);
            }
        }

        return $this->render('@App/admin/comments/index.html.twig', [
            'comments' => $comments,
            'pagination' => $pagination
        ]);
    }
}