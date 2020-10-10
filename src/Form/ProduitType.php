<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Societe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\StringToFloatTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,
                [
                    'label'=>'Nom du produit',
                    'attr'=>['placeholder'=>'Nom du produit']

                ])
            ->add('categorie',ChoiceType::class,
                [
                    'label'=>'Catégorie',
                    'choices'=>[
                        'Fruits et légumes'=>'Fruits et légumes',
                        'Viande'=>'Viande',
                        'Poisson'=>'poisson',
                        'Epicerie'=>'Epicerie',
                        'Surgelé'=>'Surgelé',
                        'Glaces'=>'Glaces',
                        'Verrerie/Vaisselle'=>'Verrerie/Vaisselle',
                        'Matériel/Consommables'=>'Matériel/Consommables',
                        'Boissons'=>'Boissons'
                    ],
                    'attr'=>[
                        'placeholder'=>'Séléctionnez la catégorie'
                    ]


                ])
            ->add('photo',TextType::class,
                [
                    'required'=>false
                ])
            ->add('prix',NumberType::class,
                [
                    'label'=>'Prix',
                    'attr'=>[
                        'placeholder'=>'Prix'
                    ]

                ])
            ->add('unite',ChoiceType::class,
                [
                    'label'=>'Unité',
                    'choices'=>[
                        'Kg'=>'Kg',
                        'piece'=>'Pièce',
                        'colis'=>'Colis'
                    ],
                    'required'=>true,
                    'expanded'=>true,
                    'multiple'=>false,
                    'placeholder'=>false
                ])
            ->add('societe', EntityType::class,
                [
                    'class'=>Societe::class,
                    'choice_label'=>'nom'
                ])
            ->add('description', TextareaType::class,
                [
                    'label'=>'Description',
                    'attr'=>[
                        'placeholder'=>'Origine, Conditionnement, si unité possibilité de colis renseigner poids moyen ou quantité...'
                    ]

                ])
            ->add('promotion',ChoiceType::class,
                [
                    'label'=>'Promotion',
                    'choices'=>[
                        'Oui'=>'Oui',
                        'Non'=>'Non'
                    ],
                    'required'=>true,
                    'expanded'=>true,
                    'multiple'=>false,
                    'placeholder'=>false
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
