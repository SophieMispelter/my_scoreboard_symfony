<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Nom",
                'constraints' => [  
                    new Length([ // new length cree l'objet de classe constraints (ie. dans le use)
                        'max' => 30,
                        'maxMessage' => "Le nom du jeu ne peut pas dépasser 30 caractères.",
                        'min' => 3,
                        'minMessage' => "Le nom du jeu doit avoir au moins 3 caractères. "
                    ]),
                    new NotBlank([
                        'message' => "Ce champ ne peut être vide."
                    ]),
                ]
            ]) // chaque methode add() rajoute un champs ds le formulaire
            ->add('min_players')
            ->add('max_players')
            ->add('enregistrer', SubmitType::class)

        ;//ces 4 methodes add() sont une seule instruction (car il y a un seul ; )
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
