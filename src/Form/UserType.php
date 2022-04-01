<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options["data"]; // je récupère la variable User qui est lié au formulaire dans le contrôleur (dans la méthode createForm())
        $builder
            ->add('pseudo', TextType::class, [
                "constraints" => [
                    new NotNull(["message" => "Veuillez saisir votre pseudo pour pouvoir vous connecter."]),
                    new Length([
                        "min" => 4,
                        "minMessage" => "Le pseudo doit comporter au moins 4 caractères.",
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'mapped' => false,
                'label' => 'E-mail',
                'constraints' => [
                    new NotNull(['message' => 'L\'e-mail ne peut pas être vide.'])
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Joueur' => 'ROLE_PLAYER',
                    'Arbitre' => 'ROLE_REFEREE',
                    'Utilisateur' => 'ROLE_USER'
                ],
                'multiple' => true,
                'expanded' => true
            ])
            ->add('password', TextType::class, [
                 'mapped' => false, // le champ password n'est pas directement relié au password du user
                //  'required' => false
                 'required' => $user->getId() ? false : true // si l'id n'est pas null, on est en modif donc required false, si l'id est null on est en new donc required true
                                                             // condition ternaire équivalent à : 'required' => !$user->getId()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
