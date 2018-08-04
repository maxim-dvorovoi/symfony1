<?php

namespace AppBundle\Controller;

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

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $auth = $this->getUser();
        if ($auth){
            $auth = $auth->getRoles();
            if ($auth[0] == 'ROLE_USER'){
                return $this->redirectToRoute('login_homepage');
            }
        }

        $economyPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5Economy();
        $policyPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5Policy();
        $financeForecPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5FinForec();
        $financeNewsPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5FinNews();
        $indexNewsPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get4indexNews();
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->FindAll();
        $analiticsPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5Analitic();
        $topComentators = $this->getDoctrine()->getRepository('AppBundle:Comments')->get5Comentators();
        $topNews = $this->getDoctrine()->getRepository('AppBundle:Comments')-> get3TopNews();

        rsort($topComentators);
        rsort($topNews);
        $topNews = array_slice($topNews,0,3);
        $topComentators = array_slice($topComentators,0,5);

        if (count($topNews) >= 3) {
            for($i=0; $i<3; $i++){
                $title = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:Post')
                    ->find($topNews[$i]['post_id'])
                    ->getTitle()
                ;
                $topNews[$i]['title'] = $title;
            }
        } else {
            for($i=0; $i<count($topNews); $i++){
                $title = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:Post')
                    ->find($topNews[$i]['post_id'])
                    ->getTitle()
                ;
                $topNews[$i]['title'] = $title;
            }
        }

        if (count($topComentators) >= 5) {
            for($i=0; $i<5; $i++){
                $username = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->find($topComentators[$i]['user_id'])
                    ->getUsername()
                ;
                $topComentators[$i]['username'] = $username;
            }
        } else {
            for($i=0; $i<count($topComentators); $i++){
                $username = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->find($topComentators[$i]['user_id'])
                    ->getUsername()
                ;
                $topComentators[$i]['username'] = $username;
            }
        }

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
                return $this->redirectToRoute('homepage');
            }
        }

        return [
            'economyPosts' => $economyPosts,
            'policyPosts' => $policyPosts,
            'financeForecPosts' => $financeForecPosts,
            'financeNewsPosts' => $financeNewsPosts,
            'indexNewsPosts' => $indexNewsPosts,
            'categories' => $categories,
            'analiticsPosts' => $analiticsPosts,
            'topComentators' => $topComentators,
            'topNews' => $topNews
        ];
    }

    /**
     * @Route("/post/{id}", name="post_item")
     * @Template()
     */
    public function showAction($id)
    {
        $auth = $this->getUser();
        if ($auth){
            $auth = $auth->getRoles();
            if ($auth[0] == 'ROLE_USER'){
                return $this->redirectToRoute('login_homepage');
            }
        }

        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $views = $post->getViews();
        $post->setViews( $views + 1);
        $this->getDoctrine()->getManager()->persist($post);
        $this->getDoctrine()->getManager()->flush();
        $tag = $post->getTags();
        $comments = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['post' => $id]);

        if (isset($_POST['like'])) {
            $votes_id = $_POST['like'];
            $comment = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['id' => $votes_id])[0];
            $votes = $comment->getVotes();
            $comment->setVotes($votes + 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);

            $entityManager->flush();
            return $this->redirectToRoute('post_item', ['id' => $id]);
        }

        if (isset($_POST['dislike'])) {
            $votes_id = $_POST['dislike'];
            $comment = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['id' => $votes_id])[0];
            $votes = $comment->getVotes();
            $comment->setVotes($votes - 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);

            $entityManager->flush();
            return $this->redirectToRoute('post_item', ['id' => $id]);
        }

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
                return $this->redirectToRoute('post_item', ['id' => $id]);
            }
        }

        return [
            'post' => $post,
            'tag' => $tag,
            'comments' => $comments
            ];
    }

    /**
     * @Route("/user", name="login_homepage")
     */
    public function loginAction()
    {
        $economyPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5Economy();
        $policyPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5Policy();
        $financeForecPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5FinForec();
        $financeNewsPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5FinNews();
        $indexNewsPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get4indexNews();
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->FindAll();
        $analiticsPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5Analitic();
        $topComentators = $this->getDoctrine()->getRepository('AppBundle:Comments')->get5Comentators();
        $topNews = $this->getDoctrine()->getRepository('AppBundle:Comments')-> get3TopNews();

        rsort($topComentators);
        rsort($topNews);
        $topNews = array_slice($topNews,0,3);
        $topComentators = array_slice($topComentators,0,5);

        if (count($topNews) >= 3) {
            for($i=0; $i<3; $i++){
                $title = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:Post')
                    ->find($topNews[$i]['post_id'])
                    ->getTitle()
                ;
                $topNews[$i]['title'] = $title;
            }
        } else {
            for($i=0; $i<count($topNews); $i++){
                $title = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:Post')
                    ->find($topNews[$i]['post_id'])
                    ->getTitle()
                ;
                $topNews[$i]['title'] = $title;
            }
        }

        if (count($topComentators) >= 5) {
            for($i=0; $i<5; $i++){
                $username = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->find($topComentators[$i]['user_id'])
                    ->getUsername()
                ;
                $topComentators[$i]['username'] = $username;
            }
        } else {
            for($i=0; $i<count($topComentators); $i++){
                $username = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->find($topComentators[$i]['user_id'])
                    ->getUsername()
                ;
                $topComentators[$i]['username'] = $username;
            }
        }

        if (isset( $_POST['submit'])) {
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
                return $this->redirectToRoute('login_homepage');
            }
        }

        return $this->render('@App/login/default/index.html.twig',[
            'economyPosts' => $economyPosts,
            'policyPosts' => $policyPosts,
            'financeForecPosts' => $financeForecPosts,
            'financeNewsPosts' => $financeNewsPosts,
            'indexNewsPosts' => $indexNewsPosts,
            'categories' => $categories,
            'analiticsPosts' => $analiticsPosts,
            'topComentators' => $topComentators,
            'topNews' => $topNews

        ]);
    }

    /**
     * @Route("/user/post/{id}", name="login_post_item")
     */
    public function showloginAction($id, Request $request)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $views = $post->getViews();
        $post->setViews( $views + 1);
        $this->getDoctrine()->getManager()->persist($post);
        $this->getDoctrine()->getManager()->flush();
        $tag = $post->getTags();
        $comments = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['post' => $id],['votes' => 'DESC']);

        $categoryId = $post->getCategory()->getId();
////
        if ($categoryId == 1){
            $moderate = new Moderate();

            $form = $this
                ->createFormBuilder($moderate)
                ->add('comment', TextType::class, array('label' => ' '))
                ->getForm()
            ;
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $comment = $form->getData()->getComment();
                $userid = $this->getUser()->getId();

                $moderate->setComment($comment);
                $moderate->setPost($id);
                $moderate->setUser($userid);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($moderate);

                $entityManager->flush();

                return $this->redirectToRoute('login_post_item', ['id' => $id]);
            }
        } else {
            $Comments = new Comments();

            $form = $this
                ->createFormBuilder($Comments)
                ->add('commentary', TextType::class, array('label' => ' '))
                ->getForm()
            ;
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $commentary = $form->getData()->getCommentary();
                $userid = $this->getUser()->getId();
                $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userid);;

                $Comments->setCommentary($commentary);
                $Comments->setPost($post);
                $Comments->setUser($user);
                $Comments->setVotes(0);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($Comments);

                $entityManager->flush();

                return $this->redirectToRoute('login_post_item', ['id' => $id]);
            }
        }

        if (isset($_POST['like'])) {
            $votes_id = $_POST['like'];
            $comment = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['id' => $votes_id])[0];
            $votes = $comment->getVotes();
            $comment->setVotes($votes + 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);

            $entityManager->flush();
            return $this->redirectToRoute('login_post_item', ['id' => $id]);
        }

        if (isset($_POST['dislike'])) {
            $votes_id = $_POST['dislike'];
            $comment = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['id' => $votes_id])[0];
            $votes = $comment->getVotes();
            $comment->setVotes($votes - 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);

            $entityManager->flush();
            return $this->redirectToRoute('login_post_item', ['id' => $id]);
        }

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
                return $this->redirectToRoute('login_post_item', ['id' => $id]);
            }
        }

        return $this->render('@App/login/default/show.html.twig',[
            'post' => $post,
            'tag' => $tag,
            'comments' => $comments,
            'form' => $form->createView(),
            'categoryId' => $categoryId
        ]);
    }


    /**
     * @Route("/admins", name="admin_homepage")
     */
    public function adminAction()
    {
        $economyPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5Economy();
        $policyPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5Policy();
        $financeForecPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5FinForec();
        $financeNewsPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5FinNews();
        $indexNewsPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get4indexNews();
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->FindAll();
        $analiticsPosts = $this->getDoctrine()->getRepository('AppBundle:Post')->get5Analitic();
        $topComentators = $this->getDoctrine()->getRepository('AppBundle:Comments')->get5Comentators();
        $topNews = $this->getDoctrine()->getRepository('AppBundle:Comments')-> get3TopNews();

        rsort($topComentators);
        rsort($topNews);
        $topNews = array_slice($topNews,0,3);
        $topComentators = array_slice($topComentators,0,5);

        if (count($topNews) >= 3) {
            for($i=0; $i<3; $i++){
                $title = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:Post')
                    ->find($topNews[$i]['post_id'])
                    ->getTitle()
                ;
                $topNews[$i]['title'] = $title;
            }
        } else {
            for($i=0; $i<count($topNews); $i++){
                $title = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:Post')
                    ->find($topNews[$i]['post_id'])
                    ->getTitle()
                ;
                $topNews[$i]['title'] = $title;
            }
        }

        if (count($topComentators) >= 5) {
            for($i=0; $i<5; $i++){
                $username = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->find($topComentators[$i]['user_id'])
                    ->getUsername()
                ;
                $topComentators[$i]['username'] = $username;
            }
        } else {
            for($i=0; $i<count($topComentators); $i++){
                $username = $this
                    ->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->find($topComentators[$i]['user_id'])
                    ->getUsername()
                ;
                $topComentators[$i]['username'] = $username;
            }
        }



        if (isset( $_POST['submit'])) {
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
                return $this->redirectToRoute('admin_homepage');
            }
        }

        return $this->render('@App/admin/default/index.html.twig',[
            'economyPosts' => $economyPosts,
            'policyPosts' => $policyPosts,
            'financeForecPosts' => $financeForecPosts,
            'financeNewsPosts' => $financeNewsPosts,
            'indexNewsPosts' => $indexNewsPosts,
            'categories' => $categories,
            'analiticsPosts' => $analiticsPosts,
            'topComentators' => $topComentators,
            'topNews' => $topNews

        ]);
    }

    /**
     * @Route("/admins/post/{id}", name="admin_post_item")
     */
    public function showAdminAction($id)
    {

        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $views = $post->getViews();
        $post->setViews( $views + 1);
        $this->getDoctrine()->getManager()->persist($post);
        $this->getDoctrine()->getManager()->flush();
        $tag = $post->getTags();
        $comments = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['post' => $id]);

        if (isset($_POST['like'])) {
            $votes_id = $_POST['like'];
            $comment = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['id' => $votes_id])[0];
            $votes = $comment->getVotes();
            $comment->setVotes($votes + 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);

            $entityManager->flush();
            return $this->redirectToRoute('admin_post_item', ['id' => $id]);
        }

        if (isset($_POST['dislike'])) {
            $votes_id = $_POST['dislike'];
            $comment = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['id' => $votes_id])[0];
            $votes = $comment->getVotes();
            $comment->setVotes($votes - 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);

            $entityManager->flush();
            return $this->redirectToRoute('admin_post_item', ['id' => $id]);
        }

        if (isset($_POST['submit'])) {
            if ($_POST['search'] == 'Политика') {
                return $this->redirectToRoute('admin_category_item', ['id' => 1]);
            } elseif ($_POST['search'] == 'Экономика') {
                return $this->redirectToRoute('admin_category_item', ['id' => 4]);
            } elseif ($_POST['search'] == 'Финансовые новости') {
                return $this->redirectToRoute('admin_category_item', ['id' => 2]);
            } elseif ($_POST['search'] == 'Финансовые прогнозы') {
                return $this->redirectToRoute('admin_category_item', ['id' => 3]);
            } elseif ($_POST['search'] == 'Топ') {
                return $this->redirectToRoute('admin_category_item', ['id' => 2]);
            } elseif ($_POST['search'] == 'Круто') {
                return $this->redirectToRoute('admin_category_item', ['id' => 1]);
            } else {
                return $this->redirectToRoute('admin_category_item', ['id' => $id]);
            }
        }

        if (isset($_POST['change'])) {
            $comment_id = $_POST['change'];
            $comment = $this->getDoctrine()->getRepository('AppBundle:Comments')->findBy(['id' => $comment_id])[0];
            $comment->setCommentary($_POST['text']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);

            $entityManager->flush();

            return $this->redirectToRoute('admin_post_item', ['id' => $id]);
        }

        return $this->render('@App/admin/default/show.html.twig',[
            'post' => $post,
            'tag' => $tag,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/admins/addPost", name="add_post")
     */
    public function addpostAction(Request $request)
    {
        $post = new Post();

        $form = $this
            ->createFormBuilder($post)
            ->add('title', TextType::class, array('label' => 'Заголовок :'))
            ->add('description', TextType::class, array('label' => 'Описание :'))
            ->add('image', TextType::class, array('label' => 'Ссылка картинки :'))
            ->add('category', ChoiceType::class, array(
                'label' => 'Категория :',
                'choices' => array(
                    'Политика' => 1,
                    'Финансовые новости' => 2,
                    'Финансовые прогнозы' => 3,
                    'Економика' => 4
                )
            ))
            ->add('tags', ChoiceType::class, array(
                'label' => 'Тег :',
                'choices' => array(
                    'Круто' => 1,
                    'Топ' => 2,
                    'Аналитика' => 3
                )
            ))
            ->add('submit', SubmitType::class, array('label' => 'Отправить'))
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form->getData()->getTitle();
            $description = $form->getData()->getDescription();
            $image = $form->getData()->getImage();
            $categoryChoise = $form->getData()->getCategory();
            $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy(['id' => $categoryChoise])[0];
            $tagsChoise = $form->getData()->getTags();
            $tags = $this->getDoctrine()->getRepository('AppBundle:Tags')->findBy(['id' => $tagsChoise])[0];

            $post->setTitle($title);
            $post->setDescription($description);
            $post->setImage($image);
            $post->setCategory($category);
            $post->setTags($tags);
            $post->setViews(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);

            $entityManager->flush();

            return $this->redirectToRoute('admin_homepage');
        }

        return $this->render('@App/admin/default/addpost.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admins/addCategory", name="add_category")
     */
    public function addcategoryAction(Request $request)
    {
        $category = new Category();

        $form = $this
            ->createFormBuilder($category)
            ->add('name', TextType::class, array('label' => 'Название Категории :'))
            ->add('submit', SubmitType::class, array('label' => 'Создать'))
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->getData()->getName();

            $category->setName($name);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);

            $entityManager->flush();

            return $this->redirectToRoute('admin_homepage');
        }

        return $this->render('@App/admin/default/addpost.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admins/addTags", name="add_tags")
     */
    public function addtagsAction(Request $request)
    {
        $tags = new Tags();

        $form = $this
            ->createFormBuilder($tags)
            ->add('name', TextType::class, array('label' => 'Название Тега :'))
            ->add('submit', SubmitType::class, array('label' => 'Создать'))
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->getData()->getName();

            $tags->setName($name);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tags);

            $entityManager->flush();

            return $this->redirectToRoute('admin_homepage');
        }

        return $this->render('@App/admin/default/addpost.html.twig',[
            'form' => $form->createView()
        ]);
    }

}
