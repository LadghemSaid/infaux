<?php

namespace App\Controller;


use App\Form\ContactType;
use App\Mercure\CookieGenerator;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\MaillingListRepository;
use Doctrine\ORM\EntityManager;
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

class IndexController extends AbstractController
{


    /**
     * Liste l'ensemble des posts triés par date de publication pour une page donnée.
     *
     * @Route("/", name="index")
     * @Template("XxxYyyBundle:Front/post:index.html.twig")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(CookieGenerator $cookieGenerator, PostRepository $postrepo, Request $request, PaginatorInterface $paginator): Response
    {

        $posts = $postrepo->findAll(); //On récupère les posts

        $posts = $paginator->paginate(
            $posts, //Donnée a paginé
            $request->query->getInt('page', 1), //Numéros de la page courante est 1 par default
        3
        );


        $response = $this->render('posts/index.html.twig', [
            'current_menu' => 'posts',
            'posts' => $posts,
            'bearerToken' => $this->getUser() ? $cookieGenerator->generateToken($this->getUser()):''

        ]);

        if ($this->getUser()) {
            $response->headers->set('set-cookie', $cookieGenerator->generate($this->getUser()));
            //$response->headers->setCookie($cookieGenerator->generate($this->getUser()));
            //dd($response->headers->getCookies());
        }
        return $response;
        //Pour 1 -> ...find($id);   avec une valeur de champ -> ...findOneBy(['title'=>'Post Du vendredi 13']);


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
     * @Route("/robot.txt")
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
