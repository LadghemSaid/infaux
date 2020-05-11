<?php

namespace App\Controller;

use App\Entity\Like;
use App\Repository\LikeRepository;
use App\Service\LikeService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/like", name="like.")
 */
class LikeController extends AbstractController
{
    private $em;
    /**
     * @var LikeService
     */
    private $likeService;
    /**
     * @var NotificationService
     */
    private $notificationService;

    public function __construct(EntityManagerInterface $em, LikeService $likeService, NotificationService $notificationService)
    {
        $this->em = $em;
        $this->likeService = $likeService;
        $this->notificationService = $notificationService;
    }

    /**
     * @Route("/add/{entity}/{id}", name="add")
     */
    public function add($entity, $id, Request $request)
    {
        $user = $this->getUser();
        $like = new Like();
        //Si l'user est null alors c'est qu'on est anonyme
        if (!$user) {
            $isLiked = $this->likeService->verify($request, $entity, $id, $type = 'ip', $user = null);
            if (!$isLiked) {
                //Enregistrement de l'ip car on est anonyme
                $like->setIp($request->getClientIps()[0]);

            } else {


                return new Response("-1");
            }
        } else {
            $isLiked = $this->likeService->verify($request, $entity, $id, $type = 'user', $user);
            if (!$isLiked) {
                //Enregistrement de l'user car un user est connecté
                $like->setUser($user);
            } else {

                return new Response("-1");

            }
        }


        //Repo vaut soit postRepository soit commentRepository
        $repo = $this->em->getRepository('App:' . ucfirst($entity));
        //$payload vaut soit un commentaire soit un post
        $payload = $repo->find($id);
        //Creation de l'appel de function custom equivaut soit a setPost ou setComment
        $func = "set" . ucfirst($entity);
        // equivaut soit a setPost($posts) ou setComment($comment)
        $like->$func($payload);

        $this->em->persist($like);
        $this->em->flush();

        if ($user) {

            //Notification pour l'user auteur du post/comment
            $this->notificationService->add($payload->getUser(), $message = "{$user->getUsername()} a aimé votre {$entity}", $user, $payload);
        } else {
            //Notification pour l'user auteur du post/comment
            $this->notificationService->add($payload->getUser(), $message = "Une personne aime votre {$entity}", $user, $payload);
        }




        //Redirection sur la page d'ou l'ont viens
        return new Response("+1");


    }

    /**
     * @Route("/delete/{entity}/{id}", name="delete")
     */
    public function delete($entity, $id, Request $request)
    {


        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
}
