<?php

namespace App\Controller;


use App\Entity\Post;
use App\Form\ContactType;
use App\Form\PostType;
use App\Mercure\CookieGenerator;
use App\Repository\CommentRepository;
use App\Repository\ConversationRepository;
use App\Repository\PostRepository;
use App\Repository\MaillingListRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Hashids\Hashids;

class IndexController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Liste l'ensemble des posts triés par date de publication pour une page donnée.
     *
     * @Route("/", name="index")
     * @Template("XxxYyyBundle:Front/post:index.html.twig")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PostRepository $postrepo, Request $request, PaginatorInterface $paginator): Response
    {

        $user = $this->getUser();

        switch ($user ? $user->getDisplaySetting() : '') {

            case 'popular':
                $posts = $postrepo->findAllByLikes(); //On récupère les posts dans l'ordre des likes

                break;

            case 'recent':
                $posts = $postrepo->findAllDesc(); //On récupère les posts dans l'ordre de creation

                break;


            case 'friends':
                $friends = $user->getFriends();
                $posts = [];
                foreach ($friends as $friend) {
                    foreach ($friend->getPosts() as $post){

                        array_push($posts,$post );
                    }
                }
                break;

            default:
                $posts = $postrepo->findAllDesc(); //On récupère les posts dans l'ordre de creation

                break;
        }

        $posts = $paginator->paginate(
            $posts, //Donnée a paginé
            $request->query->getInt('page', 1), //Numéros de la page courante est 1 par default
            3
        );

        $response = $this->render('posts/index.html.twig', [
            'current_menu' => 'posts',
            'posts' => $posts,

        ]);

        return $response;
        //Pour 1 -> ...find($id);   avec une valeur de champ -> ...findOneBy(['title'=>'Post Du vendredi 13']);


    }

    /**
     * Liste l'ensemble des posts triés par date de publication pour une page donnée.
     *
     * @Route("/chat", name="index.chat")
     */
    public function indexChat(ConversationRepository $conversationRepository,UserRepository $userRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user=$this->getUser();
        $allConversation=$conversationRepository->findConversationsByUser($user->getId());
        $allConversationUserAvatar=[];
        foreach ($allConversation as $item)
        {
            array_push($allConversationUserAvatar,[
                'username' => $item['username'],
                'image' => $userRepository->findBy(['username'=>$item['username']])[0]->getImage(),
            ]);
        }
        $notifs =$user->getNotificationsMessagerie();

        $notifsSeen = [];
        $notifsNotSeen = [];

        foreach ($notifs as $notif) {
            if ($notif->getSeen()) {
                array_push($notifsSeen, $notif);
                $notif->setSeen(true);
            } else {
                array_push($notifsNotSeen, $notif);
                $notif->setSeen(true);
            }
            $this->em->persist($notif);
        }
        $this->em->flush();
        return $this->render('chat/index/index.html.twig', [
            'current_menu' => 'chat',
            'avatar'=>json_encode($allConversationUserAvatar),

        ]);
    }


    # get success response from recaptcha and return it to controller
    function captchaverify($recaptcha)
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "secret" => "6Levh-QUAAAAAHUOIIv-s_9RbynocJYkKXZ5iE0M", "response" => $recaptcha));
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);

        return $data->success;
    }


    /**
     * @Route("/robots.txt")
     */
    public function showRobot()
    {
        return $this->render('/seo/robots.txt.twig', [
        ]);
    }


    /**
     * @Route("/sitemaps.xml", name="sitemap")
     */
    public function sitemap(Request $request, PostRepository $postRepository)
    {
        $urls = [];
        // We store the hostname of our website
        $hostname = $request->getHost();

        //$urls[] = ['loc' => $this->get('router')->generate('mywebsite_homepage'), 'changefreq' => 'weekly', 'priority' => '1.0'];
        //$urls[] = ['loc' => $this->get('router')->generate('mywebsite_blog'), 'changefreq' => 'weekly', 'priority' => '1.0'];


        $posts = $postRepository->findAll();

        foreach ($posts as $post) {
            $urls[] = ['loc' => $this->get('router')->generate('post.show', ['slug' => $post->getSlug()]), 'changefreq' => 'weekly', 'priority' => '1.0'];
        }


        // Once our array is filled, we define the controller response
        $response = new Response();
        $response->headers->set('Content-Type', 'xml');

        return $this->render('/seo/sitemaps.xml.twig', [
            'urls' => $urls,
            'hostname' => $hostname
        ]);
    }


}
