<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\PostType;
use App\Mercure\CookieGenerator;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    private $postRepository;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var NotificationService
     */
    private $notificationService;

    public function __construct(PostRepository $postRepository, EntityManagerInterface $em, NotificationService $notificationService)
    {
        $this->postRepository = $postRepository;
        $this->em = $em;
        $this->notificationService = $notificationService;
    }


    /**
     * @Route("/addpin/{id}", name="addPin")
     */
    public function addPin($id, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        //$payload vaut soit un commentaire soit un post
        $postCurr = $this->postRepository->find($id);
        if ($user->getPostFollowed()->contains($postCurr)) {
            $postCurr->removeFollowedBy($user);

            //Ce post est déja présent dans notre liste de postFollowed
            $user->removePostFollowed($postCurr);
            $this->em->persist($postCurr);
            $this->em->flush();

            return new Response("-1");

        } else {
            //Ajout de l'user dans le tableau des user du post/comment qui le suivent et recevront ainsi les notification lié a ce post/commentaire
            $postCurr->addFollowedBy($user);

            //Ajout du post dans l'user
            $user->addPostFollowed($postCurr);
            //Notification pour l'auteur du post
            $this->notificationService->add($postCurr->getUser(), $message = "un utilisateur vient de suivre votre post",$postCurr->getUser(),$postCurr);

            $this->em->persist($postCurr, $user);
            $this->em->flush();

            return new Response("+1");
        }


    }

    /**
     * @Route("/pinned", name="pinned")
     */
    public function pinned()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');



        //Cree un formulaire
        return $this->render('posts/epingle.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(PostRepository $postrepo, Request $request,$id, PaginatorInterface $paginator): Response
    {

        $postDisplay = $postrepo->find(['id'=>$id]); //On récupère les posts

        $response = $this->render('posts/show.html.twig', [
            'current_menu' => 'posts',
            'post' => $postDisplay,

        ]);

        return $response;
        //Pour 1 -> ...find($id);   avec une valeur de champ -> ...findOneBy(['title'=>'Post Du vendredi 13']);


    }

    /**
     * @Route("/follow", name="follow")
     */
    public function follow()
    {
        //Cree un formulaire
        return $this->render('follow/follow.html.twig', [
            'controller_name' => 'FollowhController',
        ]);
    }

    /**
     * @Route("/follower", name="follower")
     */
    public function follower()
    {
        //Cree un formulaire
        return $this->render('follow/follower.html.twig', [
            'controller_name' => 'FollowhController',
        ]);
    }


}
