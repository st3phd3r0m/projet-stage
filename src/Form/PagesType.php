<?php

namespace App\Form;

use App\Entity\Pages;
use App\Entity\Languages;
use App\Repository\LanguagesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PagesType extends AbstractType
{
    private $languagesRepository;

    public function __construct(LanguagesRepository $languagesRepository)
    {
        $this->languagesRepository = $languagesRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $firmSlugs=[
            'accueil',
            'qui-sommes-nous',
            'equipe-beta-testers',
            'foire-aux-questions',
            'produits-thematiques',
            'tous-les-produits'
        ];

        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre de la publication : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ])
                ]
            ])
            ->add('meta_tag_title', TextType::class, [
                'required' => true,
                'label' => 'Titre méta-donnée de la publication : ',
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
            ->add('content', CKEditorType::class, [  
                // 'config_name'=> 'main_config',  
                'required' => true,
                'label'=>'Contenu de la publication : ',
                'constraints' => [
                    new NotBlank([
                        'message'=>'Veuillez saisir le contenu de la publication',
                    ]),
                ]
            ])
            ->add('meta_tag_description', TextareaType::class, [
                'required' => true,
                'label' => 'Description en méta-données: ',
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
                'label' => 'Ajouter des mots-clés, délimités par des hashtags ("#"), afin de référencer votre publication : ',
                'mapped' => false,
                'data' => ($builder->getData()->getKeywords()!= null)? implode('#', $builder->getData()->getKeywords()) : ''
            ])
            ->add('meta_tag_keywords', TextType::class, [
                'required' => false,
                'label' => 'Ajouter des mots-clés en méta-données, délimités par des hashtags ("#"), afin de référencer votre publication : ',
                'mapped' => false,
                'data' => ($builder->getData()->getKeywords()!= null)? implode('#', $builder->getData()->getMetaTagKeywords()) : ''
            ])
            ->add('language', EntityType::class, [
                'required' => true,
                'label' => 'Choisir la langue de publication',
                'class' => Languages::class,
                'choice_label' => 'name',
                'data' => $this->languagesRepository->findOneBy(['name' => 'fr']),
            ]);

            if( !in_array($options['data']->getSlug(), $firmSlugs) ){
                $builder->add('slug', TextType::class, [
                        'required' => true,
                        'label' => 'titre ("slug") en barre d\'url : ',
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Veuillez saisir un slug.',
                            ])
                        ]
                    ]);                
            }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pages::class,
        ]);
    }
}
