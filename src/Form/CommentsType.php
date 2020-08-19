<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'required' => true,
                'label' => 'Saisissez un pseudo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Nom/pseudo requis'
                    ])
                ]
            ])
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => 'Votre avis sur cet événement',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre commentaire',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => "Votre commentaire doit comporter au moins {{ limit }}
                        caractères.",
                    ])
                ]
            ])
            ->add('mark', ChoiceType::class,[
                'required' => false,
                'label' => 'Note :',
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ],
                'label_attr'=>[
                    'class'=>'radio-inline'
                ],
                'attr'=>[
                    'class'=>'d-inline'
                ],
                'placeholder' => false,
                'choice_label'=>false,
                'expanded'=> true,
                'multiple'=> false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
