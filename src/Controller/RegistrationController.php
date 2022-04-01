<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Player;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register( Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $userPlayer = new Player(); // pour relier un objet user à un objet player: 1 --  on crée un objet player 
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setRoles(['ROLE_PLAYER']);
            
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData() //on utilise getData() pour recuperer la valeur du mdp à envoyer en bdd
                    
                )
            );

            $userPlayer->setNickname($user->getPseudo()); // 2 -- on récupere le champs commun au deux tables
            $userPlayer->setEmail( $form->get('email')->getData() ); // 3 -- on recupere le texte du FormType via getData() et ajoute à l'objet player le champ email de sa table (le champ email est obligatoire) via setEmail()-> on donne à l'objet player cette propriété
            $user->setPlayer($userPlayer); // 4 -- on relie les deux objets user et player

            $entityManager->persist($user);
            $entityManager->flush();
            
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request

            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
             
        ]);
    }
}

// EXO : restreindre les routes qui commencent par
//  	 /profil aux utilisateurs qui ont le role
//		ROLE_PLAYER => exercice resolu ds le fichier config>packages>security.yaml

