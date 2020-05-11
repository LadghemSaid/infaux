<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\User;
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
use Symfony\Component\Security\Core\Security;

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
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(PostRepository $postRepository, UserRepository $userRepository, EntityManagerInterface $em, NotificationService $notificationService)
    {
        $this->postRepository = $postRepository;
        $this->em = $em;
        $this->notificationService = $notificationService;
        $this->userRepository = $userRepository;
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
            $this->notificationService->add($postCurr->getUser(), $message = "{$postCurr->getUser()} vient d'epingler votre post", $postCurr->getUser(), $postCurr);

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
    public function show(PostRepository $postrepo, Request $request, $id, PaginatorInterface $paginator): Response
    {

        $postDisplay = $postrepo->find(['id' => $id]); //On récupère les posts

        $response = $this->render('posts/show.html.twig', [
            'current_menu' => 'posts',
            'post' => $postDisplay,

        ]);

        return $response;
        //Pour 1 -> ...find($id);   avec une valeur de champ -> ...findOneBy(['title'=>'Post Du vendredi 13']);


    }

    /**
     * @Route("/follow/{id}", name="follow")
     */
    public function follow($id)
    {

        $user = $this->userRepository->find(['id' => $id]);

        return $this->render('follow/follow.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/follower/{id}", name="follower")
     */
    public function follower($id)
    {
        $user = $this->userRepository->find(['id' => $id]);

        return $this->render('follow/follower.html.twig', [
            'user' => $user

        ]);
    }


    /**
     * @Route("/add", name="add", methods={"POST"})
     * @param Request $req
     * @param Security $security
     * @return Response
     * @throws \Exception
     */
    public function add(Request $req, PostRepository $postRepo, Security $security)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $post = new Post();


        $post->setFavorite(false)
            ->setPublished(true)
            ->setText($req->request->get('request'))
            ->setUser($user);

        $user->addPostFollowed($post);

        $this->em->persist($post);
        $this->em->persist($user);
        $this->em->flush();


        //$this->addFlash('success', "Commentaire ajouté avec succés :)");
        return  $response = $this->render('posts/only-post.html.twig', [
            'post' => $post,

        ]);


    }

    /**
     * @Route("/delete/{post}", name="delete", methods={"GET"})
     */
    public function delete(Post $post, Security $security, Request $req)
    {
//        var_dump('on est là');die;
        //   $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($security->getUser() === $post->getUser()) {
            $referer = explode('/', $req->headers->get('referer'));
            //dd($jobRepo->find($id));


        }

        return $this->redirect($req->headers->get('referer'));

        //dd($comment);

    }

}
