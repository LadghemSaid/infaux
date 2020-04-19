<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PostController extends AbstractController
{
    private $repository;
    private $repository_user;
    /**
     * @var Request
     */
    private $request;

    public function __construct(PostRepository $property_repo, UserRepository $users)
    {
        $this->repository = $property_repo;
        $this->repository_user = $users;
        $this->request = Request::createFromGlobals();
    }




    /**
     * @Route("/post/{slug}" , name="post.show", requirements={"slug"="[a-z0-9\-]*"})
     * @param string $slug
     * @param CommentRepository $commentsRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Post $post, string $slug, CommentRepository $commentsRepository)
    {
        if ($post->getSlug() !== $slug) {

            return $this->redirectToRoute('post.show', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()

            ], 301);
        }

        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('add.comment', array('id' => $post->getId())),

        ]);

        $post = $this->repository->find($post);
        $comments = $commentsRepository->findPostsComment($post->getId(), 'DESC');

        // dd($allowComment,$commentValidatingAuto );
        return $this->render('posts/show.html.twig', [
            'current_menu' => 'posts',
            'post' => $post,
            'formComment' => $formComment->createView(),
            'comments' => $comments,

        ]);

    }

    /**
     * Liste l'ensemble des posts triés par date de publication pour une page donnée.
     *
     * @Route("/", name="index")
     * @Template("XxxYyyBundle:Front/post:index.html.twig")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PostRepository $postrepo)
    {

        $posts = $postrepo->findAll(); //On récupère les posts



        //Pour 1 -> ...find($id);   avec une valeur de champ -> ...findOneBy(['title'=>'Post Du vendredi 13']);
        return $this->render('posts/index.html.twig', [
            'current_menu' => 'posts',
            'posts' => $posts,



        ]);

    }


}
