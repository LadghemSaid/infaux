<?php

namespace App\Controller;

use App\Entity\Like;
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
    public function add($entity, $id,Request $request)
    {
        $like = new Like();

        $user = $this->getUser();
        if(!$user){
            //Si l'user est null alors c'est qu'on est anonyme
           $like->setIp($request->getClientIps()[0]);
        }

        $repo = $this->em->getRepository('App:'.ucfirst($entity));
        $payload = $repo->find($id);
        $func = "set".ucfirst($entity);
        $like->$func($payload);
        $like->setUser($user);
        $this->em->persist($like);
        $this->em->flush();
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
        dd($request->get);

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
