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
    public function add(Request $req, $id, PostRepository $postRepo, Security $security, MessageBusInterface $bus)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {

            $com = $form->getData();

            $post = $postRepo->find($id);
            $com->setPost($post)
                ->setCreatedAt(new \DateTime())
                ->setApproved(true)
                ->setUser($security->getUser());
            $this->em->persist($com);
            $this->em->flush();


            //Ajout de la notification au group de gens qui suivent le post
            foreach ($post->getFollowedBy() as $user) {
                if ($user == $security->getUser()) {

                } else {
                    $this->notificationService->add($user, $message = "Un commentaire à été ajouter sur un post");
                }

            }
            //Envoie de la notif a l'auteur du post
            $this->notificationService->add($post->getUser(), $message = "Un commentaire à été ajouter sur votre post");





            //$this->addFlash('success', "Commentaire ajouté avec succés :)");
            return new Response("+1");


        }
        //$this->addFlash('error', "Un problème est survenu nous y travaillons ! :)");
        return new Response("+0");

    }

    /**
     * @Route("/delete/{comment}", name="delete", methods={"GET"})
     */
    public function delete(Comment $comment, Security $security, Request $req)
    {
        //   $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($security->getUser() === $comment->getUser()) {
            $referer = explode('/', $req->headers->get('referer'));
            //dd($jobRepo->find($id));


        }

        return $this->redirect($req->headers->get('referer'));

        //dd($comment);

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

    /**
     * @Route("/post/{post}", name="paginate")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param $commentsrepo
     * @return Response
     */
    public function getPaginateComment(Request $request, PaginatorInterface $paginator,CommentRepository $commentsrepo,$post)
    {

        $comments = $commentsrepo->findByPostField($post); //On récupère les commentaire du post
        $comments = $paginator->paginate(
            $comments, //Donnée a paginé
            $request->query->getInt('page', 1), //Numéros de la page courante est 1 par default
            3
        );
        $response = $this->render('comment/index.html.twig', [
            'current_menu' => 'comments',
            'comments' => $comments,
        ]);
        return $response;

    }

}
