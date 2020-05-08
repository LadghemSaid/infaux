<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/notification", name="notification")
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
            'notifsSeen' => $notifsSeen,
            'notifsNotSeen' => $notifsNotSeen,
        ]);
    }
}
