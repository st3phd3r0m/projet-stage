<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Categories;
use App\Entity\Languages;
use App\Repository\LanguagesRepository;
use App\Form\ImagesType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductsType extends AbstractType
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
                'label' => 'Titre de la sortie : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ]),
                ]
            ])
            ->add('meta_tag_title', TextType::class, [
                'required' => false,
                'label' => 'Titre de la sortie en méta-données : ',
            ])
            ->add('description', CKEditorType::class, [
                'config_name'=> 'main_config', 
                'required' => true,
                'label' => 'Description de la sortie : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la description de la sortie',
                    ]),
                ]
            ])
            ->add('meta_tag_description', TextareaType::class, [
                'required' => false,
                'label' => 'Description de la sortie en méta-données: ',
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
                'data' => $this->languagesRepository->findOneBy(['name' => 'fr']),
            ])
            ->add('category', CollectionType::class, [
                'required' => false,
                'label' => 'Catégories du produit',
                'entry_type' => EntityType::class,
                'entry_options'  => [
                    'choice_label' => 'title',
                    'class'=>Categories::class
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('images', CollectionType::class, [
                'required' => false,
                'label' => 'Images d\'illustration',
                'entry_type' => ImagesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference'=> false
            ])
            ->add('attribute', CollectionType::class, [
                'required' => false,
                'label' => 'Attributs du produit',
                'entry_type' => AttributesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,                
            ]);

            // ->add('Valider', SubmitType::class);

            //Ajout d'un ecouteur d'événements pour modifier l'option 'mapped'=>true en 'mapped'=>false
            //sur le champ attribut lors de la soumission du formulaire
            $builder->addEventListener(
                FormEvents::PRE_SUBMIT,
                function(FormEvent $event) {
                    $form = $event->getForm();
                    $attributeFieldoptions = $form->get('attribute')->getConfig()->getOptions();
                    $attributeFieldoptions["mapped"] = false;
                    $form->add('attribute', CollectionType::class, $attributeFieldoptions);
                    
                }
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
