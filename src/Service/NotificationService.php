<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\Post;
use App\Mercure\MercureService;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class NotificationService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var LikeRepository
     */
    private $likeRepository;
    /**
     * @var MercureService
     */
    private $mercureService;

    public function __construct(EntityManagerInterface $em, LikeRepository $likeRepository, MercureService $mercureService)
    {
        $this->em = $em;
        $this->likeRepository = $likeRepository;
        $this->mercureService = $mercureService;
    }

    public function add($user, $message = 'message par default', $byUser, $entity)
    {


        $notif = new Notification();
        $notif->setUser($user);
        $notif->setSeen(false);
        $notif->setMessage($message);
        $notif->setByUser($byUser);
        if ($entity instanceof Post) {
            $notif->setByPost($entity);
        } else if ($entity instanceof Comment) {
            $notif->setByComment($entity);
        }
        $this->em->persist($notif);
        $this->em->flush();

        $this->mercureService->addNotification($user, $message);

    }
}
