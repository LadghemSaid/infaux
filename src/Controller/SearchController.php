<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\SearchRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
//    public function search()
//    {
//        //Cree un formulaire
//        return $this->render('search/index.html.twig', [
//            'controller_name' => 'SearchController',
//        ]);
//    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, SearchRepository $repo, PaginatorInterface $paginator)
    {
        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);

        $donnees = $repo->findAll();

        if($searchForm->isSubmitted() && $searchForm->isValid()){
            $text = $searchForm->getData()->getText();

            $donnees = $repo->search($text);

            if($donnees == null){
                $this->addFlash('erreur', 'Aucun résultat trouvé pour cette recherche');
            }
        }

        $resultats = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('search/index.html.twig',[
            'resultats' => $resultats,
            'searchForm' => $searchForm->createView()
    ]);
    }
}
