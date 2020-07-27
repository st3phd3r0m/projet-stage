<?php

namespace App\Form;

use App\Entity\AttributeGroups;
use App\Entity\Attributes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AttributesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($builder->getData()->getName()){
            $name = explode('->', $builder->getData()->getName())[1];
        }

        $builder
            ->add('attribute_group', EntityType::class, [
                'required' => true,
                'label' => 'Choisir le groupe d\'attributs',
                'class' => AttributeGroups::class,
                'choice_label' => 'name',
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom de l\'attribut : ',
                'mapped' => false,
                'data'=> $name??'',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom d\'attribut.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => "Le nom doit comporter au minimum {{ limit }} caractères.",
                    ])
                ]
            ])
            ->add('value', TextareaType::class, [
                'required' => true,
                'label' => 'Contenu de l\'attribut : ',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le contenu de l\'attribut',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => "Le contenu doit comporter au minimum {{ limit }}
                    catégorie.",
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attributes::class,
        ]);
    }
}
