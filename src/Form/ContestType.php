<?php

namespace App\Form;

use App\Entity\Contest;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Game;
use App\Entity\Player;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ContestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', DateType::class, [
                'widget' => 'single_text',
                'label' => "Date de début"
            ])
            ->add('game', EntityType::class, [ // EntityType permet de creer et afficher ds le formulaire les informations de la propriété game alors que l'objet est contest 
                'class' => Game::class, // ici, le programme va chercher ds la table game de la classe (entity) Game
                'choice_label' => 'title', //choice_label et placeholder sont des attributs définis d'EntityType, ici on choisit le champ qui sera affiché dans le select
                'placeholder' => "",
                'label' => "Jeu"
            ])
            ->add('winner', EntityType::class, [
                 'class' => Player::class,
                 'choice_label' => 'nickname',
                 'placeholder' => "Choisir le gagnant...",
                 'required' => false,
                 'label' => "Vainqueur"
            ])

            ->add('players', EntityType::class, [
                  'class'=> Player::class,
                  'choice_label' => 'nickname',
                  'multiple' => true, //pour pvr selectionner plusieurs joueurs pour une partie
                  'expanded' => true, // affichage en cases à cocher
                  'label' => "Participants"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contest::class,
        ]);
    }
}
