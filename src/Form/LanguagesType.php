<?php

namespace App\Form;

use App\Entity\Languages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LanguagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Langue (FR pour français) : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une langue.',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => "La langue doit comporter au minimum {{ limit }} caractère.",
                    ])
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => $options['require_image'],
                'label' => 'Choisir un drapeau',
                'download_uri' => false,
                'imagine_pattern' => 'miniatures',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Choisir un drapeau',
                        'groups' => ['new']
                    ]),
                    new Image([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Votre image dépasse les 2Mo',
                        'mimeTypes' => ['image/png', 'image/gif', 'image/jpeg'],
                        'mimeTypesMessage' => 'Votre image doit être de type PNG, GIF ou JPEG',
                        'groups' => ['new', 'update']
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Languages::class,
            'require_image'=>true
        ]);
    }
}
