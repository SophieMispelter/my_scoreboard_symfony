<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request, GameRepository $gr): Response
    {
        // dd($request->query->get('search'));
        $word = $request->query->get('search');
        $games = $gr->findBySearch($word);
        // SELECT * FROM game WHERE title LIKE '%test%'
        return $this->render('search/index.html.twig', [
            'games' => $games,
            'nb_joueurs' => 0,
            'word' => $word
        ]);
    }
}

/* EXO 1. afficher les resultats dans le fichier search/index.html.twig sous forme de card
                afficher aussi dans une balise h1 : Résultat de la recherche pour ...
                et remplacer les ... par le mot tapé dans la barre de recherche 
  2.  trouvez cmt utiliser le même code pour les 
  		card pour le fichier home/index.html.twig
        et le fichier search/index.html.twig
                
 */
       