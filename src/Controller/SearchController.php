<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/search", name="search.")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function search()
    {
        //Cree un formulaire
        return $this->render('search/index.html.twig', [
        ]);
    }

    /**
     * @Route("/query", name="query")
     */
    public function searchAction(Request $request,PostRepository $postRepository, UserRepository $userRepository){


        $searchTerm = $request->query->get('q');
        $resultsUsers = $userRepository->findBy(['username'=> $searchTerm],null,5);
        $resultsPosts= $postRepository->findBy(['text'=> $searchTerm],null,5);


        //$results = $query->getResult();

        $content = [
            'users' => $resultsUsers,
            'posts' => $resultsPosts,
            'val' => $searchTerm
        ];

        $response = new JsonResponse();
        $response->setData(array('list' => $content));
        return $response;
    }


}
