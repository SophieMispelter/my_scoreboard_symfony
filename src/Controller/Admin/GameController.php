<?php

namespace App\Controller\Admin;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; // Request est une class qui crée un objet qui contient ttes les valeurs des superGlobales PHP et qu'on utilise par injections de dépendances
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game;
use App\Form\GameType;
// use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class GameController extends AbstractController
{
    #[Route('/admin/game', name: 'app_admin_game')]
    public function index(GameRepository $gameRepository): Response //Response est ce qui va renvoyer notre fonction
    {
        // $gameRepository = new GameRepository; ON NE PEUT PAS INSTANCIER D'OBJET D'UNE CLASSE REPOSITORY, pour l'utiliser on le passe donc en argument de la la méthode du controller index(), la methode index() sera appelée avec l'objet GameRepository. On appelle ça une injection de dependance.
        //de plus, on ajoute la classe GameRepository aux importations de use
        // NB: pour chaque classe Entity créée, il y a une classe repository qui correspond et qui permet de faire des requêtes SELECT sur la table correspondante
        return $this->render('admin/game/index.html.twig', [
            "games" => $gameRepository->findAll() // dans games il y aura des objets de la classe game, c'est la methode findall() qui permet de creer l'objet $gameRepository ce qui donne accès à la base de données et renvoie des objets de la classe game. DONC LORSQUE L' ON VEUT INTERROGER BDD, ON UTILISE LA CLASSE GAMEREPOSITORY
        ]);
    }

    #[Route('/admin/game/new', name: 'app_admin_game_new')]
    public function new(Request $request, EntityManagerInterface $em) //la classe EntityManagerInterface est l'entité qui va permettre de faire les requêtes INSERT INTO, UPDATE et DELETE. L'EntityManager va tjrs utiliser des objets Entity (cad les enregistrements ds les tables via les classes du dossier Entity) pour modifier les données
    {
        //dump($request)); //equivalent du vardump en php
         //dd($request->request->get('game[title]'); //equivalent du dump
        // La classe Request permet d'instancier un objet qui contient ttes les valeurs des variables superglobales de PHP. Ces valeurs sont des propriétés.
        // $request->query      contient     $_GET
        // $request->request    contient     $_POST
        // $request->server     contient     $_SERVER
        // etc.
        // Pour accèder aux valeurs, on utilisera sur ces propriétés la methode ->get('indice') 

        $jeu = new Game;
        $form = $this->createForm(GameType::class, $jeu); // $this fait reference au controller, cette commande créé un objet $form qui va etre basé sur le formulaire GameType et qu'on relie à l'objet $jeu. De cette manière les modifs qu'on va ajouter au formulaire vont immediatement updater les propriétes de l'objet $jeu. L'objet $form va gérer ce qui vient de la requête HTTP (avec l'objet $request et la methode handleRequest())
        $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                //dd($jeu);
                $em->persist($jeu); // méthode persist() prepare la requête INSERT avec les données de l'objet passé en argt
                $em->flush(); // methode flush() execute les requêtes en attente et donc modifie la bdd

                //redirection vers une route du projet
                return $this->redirectToRoute("app_admin_game"); 
            }



        
        return $this->render("admin/game/form.html.twig", [
            "formGame" => $form->createView() //l'objet formGame est généré automatiquement

        ]);
    }
    
    #[Route('/admin/game/edit/{id}', name: 'app_admin_game_edit')] // qd on utilise accolades ds la route, cela signifie que son contenu est variable, le id peut donc être remplacé par une autre valeur ds l'url
    public function edit(Request $rq, EntityManagerInterface $em, GameRepository $gameRepository, $id)
    {
        //dd($id);
        $jeu = $gameRepository->find($id);
        $form = $this->createForm(GameType::class, $jeu);
        $form->handleRequest($rq); // cette methode permet à l'objet $form d'acceder à la requête
        if( $form->isSubmitted() && $form->isValid())
        {
            $em->flush(); // pour req update, il n y a pas besoin de préparer (persist()), dès qu'il y a une valeur de modifier dans le form, la req est automatiquement préparée (mise à jour). Il n' y a plus qu'à l'envoyer en bdd (flush())

            return $this->redirectToRoute(("app_admin_game"));
        }
        return $this->render("admin/game/form.html.twig", ["formGame" => $form->createView()]);
    }


    #[Route('/admin/game/modifier/{title}', name: 'app_admin_game_modifier')] // qd on utilise accolades ds la route, cela signifie que son contenu est variable, le id peut donc être remplacé par une autre valeur ds l'url
    public function modifier(Request $rq, EntityManagerInterface $em, Game $jeu) //ici, on utilise la classe Game et l'objet jeu en arguments au lieu de : GameRepository $gameRepository, $title afin de récupérer un objet entité directemenr avec la valeur de cette partie ds URL. Il faut donc que le nom de ce paramèter soit le nom de la propriéte de la classe Entity. * Par exemple, le paramètre est {title}, parce que dans l'entité Game il y a une propriété title. * Dans les arguments de la méthode, on peut alors utiliser un objet de la classe Game ($jeu dans l'exemple)
    {
        //dd($id);
        // $jeu = $gameRepository->find($id);
        $form = $this->createForm(GameType::class, $jeu);
        $form->handleRequest($rq); // cette methode permet à l'objet $form d'acceder à la requête
        if( $form->isSubmitted() && $form->isValid())
        {
            $em->flush(); // pour req update, il n y a pas besoin de préparer (persist()), dès qu'il y a une valeur de modifier dans le form, la req est automatiquement préparée (mise à jour). Il n' y a plus qu'à l'envoyer en bdd (flush())

            return $this->redirectToRoute(("app_admin_game"));
        }
        return $this->render("admin/game/form.html.twig", ["formGame" => $form->createView()]);
    }

    // EXO
    // 1 - creer une route app_admin_game_delete, qui prend l'id comme parameter
    // 2 - afficher les informations du jeu à supprimer avec une nouvelle vue 
            // Confirmation de suppression du jeu suivant:
                    // titre
                    // entre nb_min et nb_max joueurs

    #[Route('/admin/game/delete/{id}', name: 'app_admin_game_delete')]
    public function delete(Request $rq, GameRepository $gameRepository, EntityManagerInterface $em, $id)
    {
        $jeu = $gameRepository->find($id);
        if($rq->isMethod("POST"))
        {
            $em->remove($jeu);
            $em->flush();
            return $this->redirectToRoute("app_admin_game");
        }
    
        return $this->render("admin/game/delete.html.twig", ["game" => $jeu ]);
    }
}

