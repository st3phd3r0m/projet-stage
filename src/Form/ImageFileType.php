<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => true,
                'label' => false,
                'download_link' => false,
                'imagine_pattern' => 'miniatures',
                // 'constraints' => [
                //     new NotBlank([
                //         'message' => 'Veuillez choisir une image de présentation',
                //         // 'groups' => ['new']
                //     ]),
                //     new Image([
                //         'maxSize' => '2M',
                //         'maxSizeMessage' => 'Votre image dépasse les 2Mo',
                //         'mimeTypes' => ['image/png', 'image/gif', 'image/jpeg'],
                //         'mimeTypesMessage' => 'Votre image doit être de type PNG, GIF ou JPEG',
                //         // 'groups' => ['new', 'update']
                //     ])
                // ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => Products::class,
        ]);
    }
}
