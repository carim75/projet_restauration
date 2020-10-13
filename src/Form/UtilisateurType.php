<?php

namespace App\Form;

use App\Entity\Societe;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo',
                TextType::class,
                [
                    'label' => 'Pseudo'
                ]
            )
            ->add('nomComplet',
                TextType::class,
                [
                    'label' => 'Nom complet'
                ]
            )
            ->add('societe', EntityType::class,
                [
                    'class'=>Societe::class,
                    'choice_label'=>'nom'
                ])
            ->add('fonction',
                ChoiceType::class,
                [
                    'label' => 'Fonction',
                    'choices' => [
                        'Restaurateur' => 'Restaurateur',
                        'Fournisseur' => 'Fournisseur',
                    ],
                    'placeholder' => 'Séléctionnez une fonction'
                ]
            )

            ->add('plainMdp',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'help' => 'Le mot de passe ne doit contenir que des lettres, des chiffres'
                            . ' et faire entre 6 et 20 caractères'
                    ],
                    'second_options' => [
                        'label' => 'Confirmez le mot de passe'
                    ],
                    'invalid_message' => 'La confirmation ne correspond pas au mot de passe'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
