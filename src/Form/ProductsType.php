<?php

namespace App\Form;

use App\Entity\AttributeGroups;
use App\Entity\Attributes;
use App\Entity\Products;
use App\Entity\Categories;
use App\Entity\Languages;
use App\Repository\LanguagesRepository;
use App\Form\ImagesType;
use App\Repository\AttributesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductsType extends AbstractType
{

    private $languagesRepository;
    private $attributesRepository;

    public function __construct(LanguagesRepository $languagesRepository, AttributesRepository $attributesRepository)
    {
        $this->languagesRepository = $languagesRepository;
        $this->attributesRepository = $attributesRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre de la sortie : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ]),
                ]
            ])
            ->add('meta_tag_title', TextType::class, [
                'required' => true,
                'label' => 'Titre de la sortie en méta-données : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => 'Description de la sortie : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la description de la sortie',
                    ]),
                ]
            ])
            ->add('meta_tag_description', TextareaType::class, [
                'required' => true,
                'label' => 'Description de la sortie en méta-données: ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la description de la sortie',
                    ]),
                ]
            ])
            ->add('hangout_location', TextType::class, [
                'required' => true,
                'label' => 'Lieu de la sortie : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un lieu pour la sortie.',
                    ]),
                ]
            ])
            ->add('keywords', TextType::class, [
                'required' => false,
                'label' => 'Ajouter des mots-clés, délimités par des hashtags ("#"), afin de référencer votre produit : ',
                'mapped' => false,
                'data' => implode('#', $builder->getData()->getKeywords())
            ])
            ->add('meta_tag_keywords', TextType::class, [
                'required' => false,
                'label' => 'Ajouter des mots-clés en méta-données, délimités par des hashtags ("#"), afin de référencer votre produit : ',
                'mapped' => false,
                'data' => implode('#', $builder->getData()->getMetaTagKeywords())
            ])
            ->add('weezeevent', TextType::class, [
                'required' => false,
                'label' => 'Ajouter un lien weezeevent afin d\'externaliser les réservations pour votre sortie : ',
            ])
            ->add('pre_tax_price', MoneyType::class, [
                'required' => false,
                'label' => 'Prix hors taxe',
            ])
            ->add('TVA', NumberType::class, [
                'required' => false,
                'label' => '%TVA',
                'data' => 20,
                'mapped' => false
            ])
            ->add('tax_included_price', MoneyType::class, [
                'required' => false,
                'label' => 'Prix TTC',
            ])
            ->add('language', EntityType::class, [
                'required' => true,
                'label' => 'Choisir la langue de publication',
                'class' => Languages::class,
                'choice_label' => 'name',
                'data' => $this->languagesRepository->findOneBy(['name' => 'FR']),
            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'label' => 'Choisir la catégorie',
                'class' => Categories::class,
                'choice_label' => 'title',
            ])
            ->add('images', CollectionType::class, [
                'required' => false,
                'label' => 'Ajoutez une ou des images d\'illustration',
                'entry_type' => ImagesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference'=> false
            ])
            ->add('attributes', CollectionType::class, [
                'required' => false,
                'mapped'=>false,
                'label' => 'Ajoutez un ou des attributs au produit',
                'entry_type' => SelectAttributeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ]);

            // ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
