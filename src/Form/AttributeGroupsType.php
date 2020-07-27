<?php

namespace App\Form;

use App\Entity\AttributeGroups;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AttributeGroupsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom du groupe d\'attributs : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom du groupe d\'attributs.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => "Le nom doit comporter au minimum {{ limit }} caractères.",
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AttributeGroups::class,
        ]);
    }
}
