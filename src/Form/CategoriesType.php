<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Languages;
use App\Repository\LanguagesRepository;
use Symfony\Component\Form\AbstractType;
use App\Form\ImagesType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoriesType extends AbstractType
{

    private $languagesRepository;

    public function __construct(LanguagesRepository $languagesRepository)
    {
        $this->languagesRepository = $languagesRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Nom de la catégorie : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom.',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => "Le nom doit comporter au minimum {{ limit }} caractères.",
                    ])
                ]
            ])
            ->add('meta_tag_title', TextType::class, [
                'required' => true,
                'label' => 'Titre méta-donnée de la catégorie : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ]),
                    new Length([
                        'min' => 50,
                        'minMessage' => "Le titre doit comporter au minimum {{ limit }} caractères.",
                        'max' => 70,
                        'maxMessage' => "Le titre doit comporter au maximum {{ limit }} caractères.",
                    ])
                ]
            ])
            ->add('description', CKEditorType::class, [  
                'config_name'=> 'main_config',  
                'required' => true,
                'label'=>'Description/contenu de la catégorie : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la description de la catégorie',
                    ]),
                ]
            ])
            ->add('meta_tag_description', TextareaType::class, [
                'required' => true,
                'label' => 'Description méta-donnée de la catégorie : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une description.',
                    ]),
                    new Length([
                        'min' => 150,
                        'minMessage' => "La description doit comporter au minimum {{ limit }} caractères.",
                        'max' => 200,
                        'maxMessage' => "La description doit comporter au maximum {{ limit }} caractères.",
                    ])
                ]
            ])
            ->add('keywords', TextType::class, [
                'required' => false,
                'label' => 'Ajouter des mots-clés, délimités par des hashtags ("#"), afin de référencer votre produit : ',
                'mapped' => false,
                'data' => ($builder->getData()->getKeywords()!= null)? implode('#', $builder->getData()->getKeywords()) : ''
            ])
            ->add('meta_tag_keywords', TextType::class, [
                'required' => false,
                'label' => 'Ajouter des mots-clés en méta-données, délimités par des hashtags ("#"), afin de référencer votre produit : ',
                'mapped' => false,
                'data' => ($builder->getData()->getKeywords()!= null)? implode('#', $builder->getData()->getMetaTagKeywords()) : ''
            ])
            ->add('language', EntityType::class, [
                'required' => true,
                'label' => 'Choisir la langue de publication',
                'class' => Languages::class,
                'choice_label' => 'name',
                'data' => $this->languagesRepository->findOneBy(['name' => 'fr']),
            ])
            ->add('images', CollectionType::class, [
                'required' => false,
                'label' => 'Images d\'illustration',
                'entry_type' => ImagesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference'=> false,
                'attr'=>[
                    'class'=>'w-50'
                ],
            ])
            ->add('slug', TextType::class, [
                'required' => true,
                'label' => 'titre ("slug") en barre d\'url : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un slug.',
                    ])
                ]
            ]);
        // ->add('image');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categories::class,
        ]);
    }
}
