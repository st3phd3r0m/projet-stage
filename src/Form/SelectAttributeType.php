<?php

namespace App\Form;

use App\Entity\AttributeGroups;
use App\Entity\Attributes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    $builder
        ->add('attribute_group', EntityType::class, [
            'required' => false,
            'mapped'=>false,
            'label' => 'Sélectionnez un groupe d\'attributs',
            'class' => AttributeGroups::class,
            'choice_label' => 'name',
            // 'by_reference'=> false
        ])
        ->add('attribute_name', EntityType::class, [
            'required' => false,
            'mapped'=>false,
            'label' => 'Sélectionnez le nom de l\'attribut',
            'class' => Attributes::class,
            'choice_label' => 'name'
        ])
        ->add('attribute_value', EntityType::class, [
            'required' => false,
            'mapped'=>false,
            'label' => 'Sélectionnez le contenu de l\'attribut',
            'class' => Attributes::class,
            'choice_label' => 'value'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AttributeGroups::class,
        ]);
    }
}
