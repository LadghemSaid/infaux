<?php

namespace App\Controller;


use App\Form\ContactType;
use App\Repository\PostRepository;
use App\Repository\MaillingListRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{





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
