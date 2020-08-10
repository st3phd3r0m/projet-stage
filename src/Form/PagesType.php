<?php

namespace App\Form;

use App\Entity\Pages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class PagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                'required' => false,
                'label' => 'Titre de la publication en méta-données : ',
            ])
            ->add('content', CKEditorType::class, [  
                'config_name'=> 'main_config',  
                'required' => true,
                'label'=>'Contenu de la publication : ',
                'constraints' => [
                    new NotBlank([
                        'message'=>'Veuillez saisir le contenu de la publication',
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
            ->add('slug', TextType::class, [
                'required' => true,
                'label' => 'titre ("slug") en barre d\'url : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un slug.',
                    ])
                ]
                
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pages::class,
        ]);
    }
}
