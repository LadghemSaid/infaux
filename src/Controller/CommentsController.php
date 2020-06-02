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
    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(EntityManagerInterface $em, NotificationService $notificationService, CommentRepository $commentRepository, PostRepository $postRepository)
    {
        $this->em = $em;

        $this->notificationService = $notificationService;
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/add/{id}", name="add", methods={"POST"})
     * @param Request $req
     * @param Security $security
     * @return Response
     * @throws \Exception
     */
    public function add(Request $req, $id, PostRepository $postRepo, Security $security, CommentRepository $commentRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $lastCommentUser = $commentRepository->findOneBy(['user' => $user], ['created_at' => 'DESC']);
        if ($lastCommentUser) {
            $now = new \DateTime();

            $last = $lastCommentUser->getCreatedAt();
            $dif = $last->diff($now);
            $hourElapsed = $dif->format('%H');
            $minuteElapsed = $dif->format('%i');
            $secondElapsed = $dif->format('%s');
            $difFormated = $hourElapsed . $minuteElapsed . $secondElapsed;
            //Un commentaire toutes les 10 secondes
            if ($difFormated < "000010") {
                return new Response("spam");
            }
        }
        $textComment = $req->request->get('textComment');

        if (strlen($textComment) < 1) {
            return new Response("lengthTooShort");
        } elseif (strlen($textComment) > 500) {
            return new Response("lengthTooLong");
        }

        $comment = new Comment();

        $post = $postRepo->find($id);
        $comment->setPost($post)
            ->setCreatedAt(new \DateTime())
            ->setApproved(true)
            ->setUser($user)
            ->setIsReply(false)
            ->setTextComment($textComment);

        if ($req->request->get('replyId') !== "") {
            $replyingComment = $this->commentRepository->findBy(['id' => $req->request->get('replyId')]);
            $comment->addReplyComment($replyingComment[0]);
            $comment->setIsReply(true);
        }

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
        $this->notificationService->add($post->getUser(), $message = "Un commentaire a été ajouté à ton post !",
            $user, $post);


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
        if ($security->getUser() === $comment->getUser()  || $this->isGranted('ROLE_ADMIN') ) {
            $this->em->remove($comment);
            $this->em->flush();

            return new Response("-1");
        } else {
            return new Response("-0");

        }

    }

    /**
     * @Route("/desactivate/comment/{comment}", name="desactivate", methods={"GET"})
     */
    public function desactivate(Comment $comment, Security $security, Request $req)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isGranted('ROLE_ADMIN') ) {
            $comment->setPublished(false);
            $this->em->persist($comment);
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
