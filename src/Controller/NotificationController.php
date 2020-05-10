<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/notification", name="notification.")
 */
class NotificationController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    public function __construct(EntityManagerInterface $em,NotificationRepository $notificationRepository)
    {
        $this->em = $em;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $notifs = $this->getUser()->getNotifications();

        $notifsSeen = [];
        $notifsNotSeen = [];

        foreach ($notifs as $notif) {
            if ($notif->getSeen()) {
                array_push($notifsSeen, $notif);
                $notif->setSeen(true);
            } else {
                array_push($notifsNotSeen, $notif);
                $notif->setSeen(true);

            }
            $this->em->persist($notif);
        }
        $this->em->flush();
        return $this->render('notification/index.html.twig', [
            'current_menu' => 'notification',
            'notifsSeen' =>  array_reverse($notifsSeen),
            'notifsNotSeen' =>  array_reverse($notifsNotSeen),
        ]);
    }

    /**
     * @Route("/deleteAll", name="deleteAll")
     */
    public function deleteAll()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $notifications = $this->notificationRepository->findAll();
        foreach ($notifications as $notif){
            $this->em->remove($notif);
        }
        $this->em->flush();
        return new Response("-1");
    }

    /**
     * @Route("/delete/{notification}", name="delete")
     */
    public function delete(Notification $notification)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $notification = $this->notificationRepository->find($notification);
        $this->em->remove($notification);
        $this->em->flush();

        return new Response("-1");
    }
}
