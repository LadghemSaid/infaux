<?php

namespace App\Controller;

use App\Entity\Like;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/like", name="like.")
 */
class LikeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/add/{entity}/{id}", name="add")
     */
    public function add($entity, $id, Request $request, LikeRepository $likeRepository)
    {
        $user = $this->getUser();
        $like = new Like();
        if(!$user) {
            //Verification de la présence d'un vote dans la db pour l'entité soit post ou comment
            $test = $likeRepository->findOneBy(['ip' => $request->getClientIps()[0], $entity=> $id]);


            if($test) {
                //Si on entre ici c'est que le post/comment est deja liké et qu'on le supprime(ou ne fait rien)
                $this->em->remove($test);
                $this->em->flush();
                //Redirection sur la page d'ou l'ont viens
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            //Si l'user est null alors c'est qu'on est anonyme
            $like->setIp($request->getClientIps()[0]);
        }

        $test = $likeRepository->findOneBy(['user' => $user,$entity => $id]);
        if($test) {
            //Si on entre ici c'est que le post/comment est deja liké et qu'on le supprime(ou ne fait rien)
            $this->em->remove($test);
            $this->em->flush();

            //Redirection sur la page d'ou l'ont viens
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);

        }

        //Repo vaut soit postRepository soit commentRepository
        $repo = $this->em->getRepository('App:' . ucfirst($entity));
        //$payload vaut soit un commentaire soit un post
        $payload = $repo->find($id);
        //Creation de l'appel de function custom equivaut soit a setPost ou setComment
        $func = "set" . ucfirst($entity);
        // equivaut soit a setPost($posts) ou setComment($comment)
        $like->$func($payload);
        //Enregistrement de l'user car un user est connecté
        $like->setUser($user);

        $this->em->persist($like);
        $this->em->flush();

        //Redirection sur la page d'ou l'ont viens
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);

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
