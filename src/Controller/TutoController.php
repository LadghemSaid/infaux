<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TutoController extends AbstractController
{
    /**
     * @Route("/inscription/avatar", name="tuto.avatar")
     */
    public function avatar()
    {
        //Cree un formulaire
        return $this->render('tuto/avatar.html.twig', [
            'controller_name' => 'TutoController',
        ]);
    }

    /**
     * @Route("/inscription/description", name="tuto.description")
     */
    public function description()
    {
        return $this->render('tuto/description.html.twig', [
            'controller_name' => 'TutoController',
        ]);
    }

    /**
     * @Route("/inscription/skip", name="tuto.skip")
     */
    public function skip()
    {
        return $this->render('tuto/skip.html.twig', [
            'controller_name' => 'TutoController',
        ]);
    }
}
