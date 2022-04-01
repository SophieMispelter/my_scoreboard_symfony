<?php

namespace App\Controller;

use App\Entity\Game;

use App\Entity\Contest;
use App\Form\CommencerPartieType;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GameRepository $gameRepository, PlayerRepository $playerRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'games' => $gameRepository->findAll(),
            'nb_joueurs' => count($playerRepository->findAll()),
            'winner' => $playerRepository->findWinners()

        ]);
    }

    #[Route("/commencer-une-partie-{title}", name: "app_home_contest")]
    public function commencerPartie(Game $jeu, EntityManagerInterface $em, Request $request)
    {
        $partie = new Contest;
        $partie->setGame($jeu);
        $form = $this->createForm(CommencerPartieType::class, $partie); // toute modification apportée à $partie modifiera l'objet 
        $form->handlerequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            // $em= $this->getDoctrine()->getManager(); ancienne méthode de récuperer l'EntityManager ds version ancienne de Symfony
            $em->persist($partie);
            $em->flush();

            //message flash: messages qui sont éffacés à la fin de la session
            $this->addFlash("success", "La nouvelle partie a bien été enregistrée"); 
            // addFlash(arg 1 type, arg2 messages)
            // $this->addFlash("danger", "Message d'erreur");
            // $this->addFlash("info", "Message d'info");


            return $this->redirectToRoute("app_home");
        }
        return $this->render("home/commencer.html.twig",[
            "form" => $form->createView()
        ]);

    }
}

  