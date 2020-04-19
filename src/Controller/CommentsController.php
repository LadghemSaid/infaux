<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/comment/", name="comment.")
 */
class CommentsController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

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


            $this->addFlash('succes', "Commentaire ajouté avec succés :)");

            return $this->redirectToRoute('index', array('slug' => $post->getSlug()));


        }
        $this->addFlash('error', "Un problème est survenu nous y travaillons ! :)");
        return $this->redirect($req->headers->get('referer'));

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

}
