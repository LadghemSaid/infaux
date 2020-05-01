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

    public function __construct(PostRepository $postRepository, EntityManagerInterface $em, NotificationService $notificationService)
    {
        $this->postRepository = $postRepository;
        $this->em = $em;
        $this->notificationService = $notificationService;
    }


    /**
     * @Route("/addpin/{id}", name="addPin")
     */
    public function addPin($id, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        //$payload vaut soit un commentaire soit un post
        $postCurr = $this->postRepository->find($id);
        if ($user->getPostFollowed()->contains($postCurr)) {
            $postCurr->removeFollowedBy($user);

            //Ce post est déja présent dans notre liste de postFollowed
            $user->removePostFollowed($postCurr);
            $this->em->persist($postCurr);
            $this->em->flush();

            return new Response("-1");

        } else {
            //Ajout de l'user dans le tableau des user du post/comment qui le suivent et recevront ainsi les notification lié a ce post/commentaire
            $postCurr->addFollowedBy($user);

            //Ajout du post dans l'user
            $user->addPostFollowed($postCurr);
            //Notification pour l'auteur du post
            $this->notificationService->add($postCurr->getUser(), $message = "un utilisateur vient de suivre votre post");

            $this->em->persist($postCurr, $user);
            $this->em->flush();

            return new Response("+1");
        }


    }

    /**
     * @Route("/deletepin/{id}", name="deletePin")
     */
    public function deletePin($id, Request $request, $payload = null)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


    }

        /**
     * @Route("/epingle", name="epingle")
     */
    public function epingle()
    {
        //Cree un formulaire
        return $this->render('posts/epingle.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }


}
