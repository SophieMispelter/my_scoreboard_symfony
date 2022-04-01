<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Player;
use App\Form\UserType;
use App\Repository\PlayerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as Hasher;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, Hasher $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if( !in_array("ROLE_ADMIN", $user->getRoles()))
            {
                $newPlayer = new Player;
                $newPlayer->setNickname($user->getPseudo());
                $newPlayer->setEmail($form->get('email')->getData());
                $user->setPlayer($newPlayer);
            }
            else
            {
                $user->setRoles(['ROLE_ADMIN']);
            }
        
            //Autre façon de récuperer l'email:
            //$newPlayer->setEmail($request->request->get("email"));
            // on est en méthode POST ds le formulaire donc : $request->request

            $mdp = $form->get('password')->getData();
            $password = $hasher->hashPassword($user, $mdp);
            $user->setPassword($password);
            $userRepository->add($user);
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'], requirements: ['id'=>'[0-9]+'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, Hasher $hasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if( !in_array("ROLE_ADMIN", $user->getRoles()))
            {
                $newPlayer = new Player;
                $newPlayer->setNickname($user->getPseudo());
                $newPlayer->setEmail($form->get('email')->getData());
                $user->setPlayer($newPlayer);
            }
            else
            {
                $user->setRoles(['ROLE_ADMIN']);
            }

            if($mdp = $form->get('password')->getData())
            {
                $password = $hasher->hashPassword($user, $mdp);
                $user->setPassword($password);
            }

            $userRepository->add($user);
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

  #[Route('/update', name: 'app_admin_user_update')]
public function update( UserRepository $userRepository, EntityManagerInterface $em) : Response
{
    // $user = new User();
    $users = $userRepository->findAll(); // recuperation de tous les users // findAll() renvoie un array
 
    $compteur = 0; //init du comptage du nombre de modifications
    
        foreach( $users as $user) // boucle foreach pour parcourir l'array $users (Parametre1: $users est la variable sur laquelle on fait la boucle / on parcourt le tableau) // la variable $user est celle qui va changer de valeur à chaque tour de boucle, $user sera à chaque tour un objet de la table $users.
        {
            if( in_array("ROLE_ADMIN", $user->getRoles()) && $user->getPlayer() == null  )
            {
                // if($user->getPlayer() == null)//verification : si user en question à un player ou pas (si $user est null, il n'y a donc pas d'objet player relié). Comme player est une propriété privé de l'entité User, on utilise la méthode getPlayer() pour savoir ce qu'il y a comme propriétés de l'objet player
                // equivalent à if(!user->getPlayer())
                // {
                    // dump('player_null');
                    $playerUser = new Player(); 
                    $playerUser->setNickname($user->getPseudo());
                    $playerUser->setEmail( $user->getPseudo() . '@yopmail.com'); 
                    
                    $user->setPlayer($playerUser);
                    $compteur++;
                // }
            }
            // else
            // {
            //     // dump('player_not_null');
            // }
               
        }
        // dd('stop');

        $em->flush(); // on ajoute a classe EntityManagerInterface $em pour pouvoir envoyer les requetes en bdd. On n'a pas besoin de persist() car $user->setPlayer($playerUser); relie les propriétés de user à player
        $this->addFlash('success' , 'Mise à jour réussie! ' . $compteur . ' joueur.s créé.s.');

    return $this->redirectToRoute('app_admin_player_index');
}
}

// EXERCICE:
// 1 -- Rajouter une route: name='app_admin_user_update'
// 2 -- Dans cette route récuperer tous les users. Vérifier pour chaque user s'il y a un player relié ou pas.
// Si il n'y a pas de player relié:
        //  créez un nouveau player (son nickname sera egal au pseudo de l'user, l'email sera pseudo@yopmail.com),
        //  reliez ce player au user, et
        //  enregistrez les modifications en bdd
    // Faites une redirection vers la page liste des users avec un message d'alerte success: 'Mise à jour réussie'


// EXO pour vendredi 1er Avril : 
// 1. Dans la liste des Users : ajoutez une colonne E-mail et affichez les e-mails du player qui est lié
// 2. Quand un admin ajoute un USER, il faut que cela crée automatiquement un PLAYER  (comme pour l'inscription) 
// 	Dans le formulaire, ajouteur un champ email (comme pour l'inscription)


  

