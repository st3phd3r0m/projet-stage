<?php

namespace App\Form;

use App\Entity\People;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image;

class PeopleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom du collaborateur : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom.',
                    ]),
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => 'Text d\'introduction du collaborateur: ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une description',
                    ]),
                ]
            ])
            ->add('quote', TextType::class, [
                'required' => false,
                'label' => 'Citation du collaborateur : ',
            ])
            ->add('cite', TextType::class, [
                'required' => false,
                'label' => 'Auteur cité : ',
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Photo du collaborateur',
                'download_uri' => false,
                'imagine_pattern' => 'miniatures',
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Votre image dépasse les 2Mo',
                        'mimeTypes' => ['image/png', 'image/gif', 'image/jpeg'],
                        'mimeTypesMessage' => 'Votre image doit être de type PNG, GIF ou JPEG',
                    ])
                ]
            ])
            ->add('function', TextType::class, [
                'required' => false,
                'label' => 'fonction du collaborateur : ',
            ])
            ->add('is_head', CheckboxType::class, [
                'label'    => 'Fait parti de la direction ?',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => People::class,
        ]);
    }
}
