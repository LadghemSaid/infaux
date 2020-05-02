<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/follow", name="follow.")
 */
class FollowController extends AbstractController
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

    public function __construct(EntityManagerInterface $em, NotificationService $notificationService)
    {
        $this->em = $em;
        $this->notificationService = $notificationService;
    }

    /**
     * @Route("/add/{id}", name="add")
     */
    public function add($id, Request $request, UserRepository $userRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $friend = $userRepository->find(['id' => $id]);
        if ($user->getFriends()->contains($friend)) {
            //Amis déjà présent on le retire
            $user->removeFriend($friend);
            $this->em->persist($user);
            $this->em->flush();

            return new Response("-1");

        } else {
            //Ajout de l'amis

            $user->addFriend($friend);

            $this->em->persist($user);
            $this->em->flush();

            //Notification pour l'user suivis
            $this->notificationService->add($friend, $message = "{$user->getUsername()} vous suit !",$user,$user);

            return new Response("+1");

        }


    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($entity, $id, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

        /**
     * @Route("/follow", name="follow")
     */
    public function follow()
    {
        //Cree un formulaire
        return $this->render('follow/follow.html.twig', [
            'controller_name' => 'FollowhController',
        ]);
    }

    /**
     * @Route("/follower", name="follower")
     */
    public function follower()
    {
        //Cree un formulaire
        return $this->render('follow/follower.html.twig', [
            'controller_name' => 'FollowhController',
        ]);
    }
}
