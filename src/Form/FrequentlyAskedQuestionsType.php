<?php

namespace App\Form;

use App\Entity\FrequentlyAskedQuestions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FrequentlyAskedQuestionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', TextType::class, [
                'required' => true,
                'label' => 'Question souvent posée : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un texte.',
                    ]),
                ]
            ])
            ->add('answer', TextareaType::class, [
                'required' => true,
                'label' => 'Réponse : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un texte.',
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrequentlyAskedQuestions::class,
        ]);
    }
}
