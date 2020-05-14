<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Form\CommentType;
use App\Mercure\MercureService;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/comment", name="comment.")
 */
class CommentsController extends AbstractController
{
    private $em;
    /**
     * @var NotificationService
     */
    private $notificationService;

    public function __construct(EntityManagerInterface $em, NotificationService $notificationService)
    {
        $this->em = $em;

        $this->notificationService = $notificationService;
    }

    /**
     * @Route("/add/{id}", name="add", methods={"POST"})
     * @param Request $req
     * @param Security $security
     * @return Response
     * @throws \Exception
     */
    public function add(Request $req, $id, PostRepository $postRepo, Security $security)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $comment = new Comment();
        $user = $this->getUser();

        $post = $postRepo->find($id);
        $comment->setPost($post)
            ->setCreatedAt(new \DateTime())
            ->setApproved(true)
            ->setUser($user)
            ->setTextComment($req->request->get('request'));

        $this->em->persist($comment);
        $this->em->flush();


        //Ajout de la notification au group de gens qui suivent le post
        foreach ($post->getFollowedBy() as $userFollorwing) {
            if ($userFollorwing == $security->getUser()) {

            } else {
                $this->notificationService->add($userFollorwing, $message = "{$user->getUsername()} a commenter sur ce post epinglé", $user, $post);
            }

        }
        //Envoie de la notif a l'auteur du post
        $this->notificationService->add($post->getUser(), $message = "Un commentaire à été ajouter sur votre post", $user, $post);


        //$this->addFlash('success', "Commentaire ajouté avec succés :)");
        return $response = $this->render('comment/only-comment.html.twig', [
            'comment' => $comment,

        ]);


    }

    /**
     * @Route("/delete/{comment}", name="delete", methods={"GET"})
     */
    public function delete(Comment $comment, Security $security, Request $req)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($security->getUser() === $comment->getUser()) {
            $this->em->remove($comment);
            $this->em->flush();

            return new Response("-1");
        } else {
            return new Response("-0");

        }

    }

    /**
     * @Route("/edit/{comment}", name="edit")
     */
    public function edit(Comment $comment, Request $request)
    {

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        // dd($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTextComment($form["textComment"]->getData());
            $this->em->flush();

        }
        return new Response;
    }

}
