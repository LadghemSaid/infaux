<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Report;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/report", name="report.")
 */
class ReportController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/add/{entity}/{id}", name="add")
     */
    public function add($entity, $id, Request $request, ReportRepository $reportRepository)
    {

        $user = $this->getUser();
        $report = new Report();
        if (!$user) {
            //Verification de la présence d'un report dans la db pour l'entité soit post ou comment avec l'ip actuelle
            $test = $reportRepository->findOneBy(['ip' => $request->getClientIps()[0], $entity => $id]);


            if ($test) {
                //Si on entre ici c'est que le post/comment est deja reporté et qu'on le supprime(ou ne fait rien)
                //$this->addFlash('error', "Vous ne pouvez pas signalez deux fois le meme contenu");
                //Redirection sur la page d'ou l'ont viens
                return new Response("+0");
            }

            //Si l'user est null alors c'est qu'on est anonyme
            $report->setIp($request->getClientIps()[0]);
        } else {
            $test = $reportRepository->findOneBy(['user' => $user, $entity => $id]);
            if ($test) {
                //Si on entre ici c'est que le post/comment est deja reporté et qu'on le supprime(ou ne fait rien)

                //$this->addFlash('error', "Vous ne pouvez pas signalez deux fois le meme contenu");

                //Redirection sur la page d'ou l'ont viens
                return new Response("+0");

            }
        }


        //Repo vaut soit postRepository soit commentRepository
        $repo = $this->em->getRepository('App:' . ucfirst($entity));
        //$payload vaut soit un commentaire soit un post
        $payload = $repo->find($id);
        //Creation de l'appel de function custom equivaut soit a setPost ou setComment
        $func = "set" . ucfirst($entity);
        // equivaut soit a setPost($posts) ou setComment($comment)
        $report->$func($payload);
        //Enregistrement de l'user car un user est connecté
        $report->setUser($user);

        $this->em->persist($report);
        $this->em->flush();

        //Modification du nombre de report pour le post ou comment
        if ($payload instanceof Post && count( $payload->getReports()) > 2) {
            $payload->setPublished(false);
            $this->em->persist($payload);
            $this->em->flush();
        } else if ($payload instanceof Comment && $payload->getReport() > 2) {
            //modification du status approved si commentaire et published si post
            $payload->setApproved(false);
            $this->em->persist($payload);
            $this->em->flush();
        }



        //Redirection sur la page d'ou l'ont viens
        //$this->addFlash('success', "Merci de votre contribution");

        return new Response("+1");
    }
}
