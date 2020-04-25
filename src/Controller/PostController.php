<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Mercure\CookieGenerator;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(PostRepository $postRepository, EntityManagerInterface $em,NotificationService $notificationService)
    {
        $this->postRepository = $postRepository;
        $this->em = $em;
        $this->notificationService = $notificationService;
    }


    /**
     * @Route("/addfollow/{entity}/{id}", name="addfollow")
     */
    public function addFollow($entity, $id, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //Repo vaut soit postRepository soit commentRepository
        $repo = $this->em->getRepository('App:' . ucfirst($entity));
        //$payload vaut soit un commentaire soit un post
        $payload = $repo->find($id);
        foreach ($this->getUser()->getPostFollowed() as $post) {
            if($post === $payload){
                //Ce post est déja présent dans notre liste de postFollowed
                $this->deleteFollow($entity, $id, $request,$payload);
                return new Response("-1");
            }
        }

        //Ajout de l'user dans le tableau des user du post/comment qui le suivent et recevront ainsi les notification lié a ce post/commentaire
        $payload->addFollowedBy($this->getUser());

        //Notification pour l'auteur du post
        $this->notificationService->add( $payload->getUser(), $message="un utilisateur vient de suivre votre post");


        $this->em->persist($payload);
        $this->em->flush();

        return new Response("+1");

    }

    /**
     * @Route("/deletefollow/{entity}/{id}", name="deletefollow")
     */
    public function deleteFollow($entity, $id, Request $request, $payload=null)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $func = "remove" . ucfirst($entity)."Followed";
        $this->getUser()->$func($payload);
        $this->em->persist($payload);
        $this->em->flush();
    }


}
