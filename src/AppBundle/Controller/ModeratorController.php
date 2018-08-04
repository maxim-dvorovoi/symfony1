<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Category;
use AppBundle\Entity\Comments;
use AppBundle\Entity\Moderate;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tags;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ModeratorController extends Controller
{
    /**
     * @Route("/admins/moderate", name="admin_moderate")
     */
    public function indexAction(Request $request)
    {
        $moderates = $this->getDoctrine()->getRepository('AppBundle:Moderate')->findAll();

        if (isset($_POST['add'])) {
            $comments = new Comments();

            $moderate_id = $_POST['add'];
            $moderate = $this->getDoctrine()->getRepository('AppBundle:Moderate')->findBy(['id' => $moderate_id])[0];
            $comment = $moderate->getComment();
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(['id' => $moderate->getUser()])[0];
            $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(['id' => $moderate->getPost()])[0];

            $comments->setCommentary($comment);
            $comments->setUser($user);
            $comments->setPost($post);
            $comments->setVotes(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comments);
            $entityManager->remove($moderate);

            $entityManager->flush();

            return $this->redirectToRoute('admin_moderate');
        }

        if (isset($_POST['delete'])) {
            $moderate_id = $_POST['delete'];
            $moderate = $this->getDoctrine()->getRepository('AppBundle:Moderate')->findBy(['id' => $moderate_id])[0];

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($moderate);

            $entityManager->flush();
            return $this->redirectToRoute('admin_moderate');
        }

        return $this->render('@App/admin/moderator/index.html.twig',[
            'comments' => $moderates
        ]);
    }
}