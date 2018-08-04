<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $auth = $this->getUser();
        if ($auth){
            $auth = $auth->getRoles();
            if ($auth[0] == 'ROLE_USER'){
                return $this->redirectToRoute('login_homepage');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

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
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('@App/security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/admins/login", name="adminslogin")
     */
    public function loginAdminAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

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
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('@App/security/admins.login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
}