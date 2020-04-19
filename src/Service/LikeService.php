<?php

namespace App\Service;

use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class LikeService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var LikeRepository
     */
    private $likeRepository;

    public function __construct(EntityManagerInterface $em, LikeRepository $likeRepository)
    {
        $this->em = $em;
        $this->likeRepository = $likeRepository;
    }

    public function verify(Request $request, $entity, $id, $type = 'user', $user = null)
    {

        if ($type === "ip") {
            //Verification de la présence d'un vote dans la db pour l'entité soit post ou comment
            $test = $this->likeRepository->findOneBy(['ip' => $request->getClientIps()[0], $entity => $id]);

            if ($test) {
                //Si on entre ici c'est que le post/comment est deja liké et qu'on le supprime(ou ne fait rien)
                $this->em->remove($test);
                $this->em->flush();
                return true;
            }
            return false;


        } else {
            $test = $this->likeRepository->findOneBy(['user' => $user, $entity => $id]);
            if ($test) {
                //Si on entre ici c'est que le post/comment est deja liké et qu'on le supprime(ou ne fait rien)
                $this->em->remove($test);
                $this->em->flush();
                return true;
            }
            return false;
        }
    }
}
