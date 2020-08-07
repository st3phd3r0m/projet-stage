<?php

namespace App\Form;

use App\Entity\Messages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Saisissez vos nom et prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir vos nom et prénom'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une adresse e-mail valide'
                    ]),
                    new Email([
                        'message' => 'Veuillez saisir une adresse e-mail valide'
                    ])
                ]
            ])
            ->add('phone', TelType::class, [
                'required' => true,
                'label' => 'Télephone',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un numéro de télephone'
                    ])
                ]
            ])
            ->add('subject', TextType::class, [
                'required' => true,
                'label' => 'Saisissez l\'objet de votre demande',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champs recquis'
                    ])
                ]
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'label' => 'Saisissez votre message',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre message',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => "Votre commentaire doit comporter au moins {{ limit }}
                        caractères.",
                    ])
                ]
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Messages::class,
        ]);
    }
}
