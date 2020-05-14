<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function searchAction(Request $request, PostRepository $postRepository, UserRepository $userRepository)
    {


        $searchTerm = $request->query->get('q');
        $resultsUsers = $userRepository->findUsersByString($searchTerm);
        $resultsPosts = $postRepository->findPostsByString($searchTerm);


        if (count($resultsPosts) > 0) {
            $renderResultsPosts=[];
            foreach ($resultsPosts as $post){
                array_push($renderResultsPosts, $this->renderView('posts/only-post.html.twig', [
                    'post' => $post,
                ]));
            }


        }else{
            $renderResultsPosts = [];
        }
        if (count($resultsUsers) > 0) {
            $renderResultsUsers = $this->renderView('search/only-user.html.twig', [
                'users' => $resultsUsers,
            ]);
        }else{
            $renderResultsUsers=[];
        }


        $content = [
            'users' => $renderResultsUsers,
            'posts' => $renderResultsPosts,
            'val' => $searchTerm
        ];

        $response = new JsonResponse();
        $response->setData(array('list' => $content));
        return $response;
    }


}
