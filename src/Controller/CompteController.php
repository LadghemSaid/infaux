<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class CompteController extends AbstractController
{
    /**
     * @Route("/compte/{id}", name="compte")->where('id','[0-9]+');
     */
    public function index()
    {
         $user = $this->getUser();

        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
            'user'=> $user,
        ]);
    }
}
