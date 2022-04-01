<?php

namespace App\Controller; // App = dossier src. Tous les namespace qui ne commencent pas par App sont situés ds le dossier Vendor

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response; // use : permet d'utiliser la classe "Response", tout ce qui est avant est le chemin pour trouver la classe. "Response" devient l'alias qui sera utilisé pour appeler cette classe
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')] //genere une URL qui va être liée au controller à executer, si on souhaite rajouter un affichage (donc une route), par ex: #[Route('/test-nouvelleroute', name: 'app_test')] : il faudra ajouter une methode dans le controller
    
    public function index(): Response // tous les controller ont la methode index()
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'Bonjour',
            'texte' => 'Premier texte'
        ]); // render() -> methode qui sert à faire l'affichage où le paramater est le fichier qui va nous servir à l'affichage. La variable (ici controller_name) est appelée dans le fichier html.twig (le but de twig est de remplacer le php)
    }

     //Exercice: Ajouter une route pour le chemin '/test/calcul' qui utilise le fichier test/index.html.twig et qui affiche le resultat de 12 + 7 

    #[Route('/test/calcul', name: 'app_test/calcul')]
    public function calcul(): Response
    {
        $a = 12;
        $b = 7;
        return $this->render('test/index.html.twig', [
            'controller_name' => 'Bonjour',
            'texte' => 'Premier texte',
            'addition' => $a + $b
        ]);
    }

    // Exercice: Ajouter une route dans le controleur Test : url "/test/salut" et une methode salut()
    // dans un nouvel affichage (donc un nouveau fichier twig)
        // Afficher "Salut $prenom" (changer le contenu du block body, mais ne pas changer le contenu des autres blocks)

        #[Route('/test/salut', name: 'app_test/salut')] // on peut avoir autant de routes qu'on veut dans un controller
        public function salut(): Response
        {
            $prenom = 'Didier';
            return $this->render('test/index_2.html.twig', [
                'prenom' => $prenom,
                
            ]);
        }

        #[Route('/test/tableau', name: 'app_test/tableau')]
        public function tableau()
        {
            $array = ["Bonjour", "je m'appelle", 789, true, 12, 38];
            return $this->render("test/tableau.html.twig", [
                                "tableau" => $array
            ]);
        }

        #[Route('/test/tableau-assoc')]
        public function tab()
        {
            $p = [
                "nom" => "Cérien",
                "prenom" => "Jean",
                "age" => 32
            ];

            return $this->render("test/assoc.html.twig", ["personne" => $p]);
            //EXO: Afficher "Je m'appelle" suivi du pénom et du nom dans le tableau
        }

        #[Route('/test/objet')]
        public function objet()
        {
            $objet = new \stdClass; // on met le back slash pc pas ds le use
            $objet->prenom = "Nordine";
            $objet->nom = "Ateur";
            $objet->age = 40;
            return $this->render("test/assoc.html.twig", ["personne" => $objet]);
        }


       
   
}
