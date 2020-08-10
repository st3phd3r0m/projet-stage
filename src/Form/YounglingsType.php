<?php

namespace App\Form;

use App\Entity\Younglings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image;

class YounglingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'nom de l\'***REMOVED*** : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom.',
                    ]),
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Photo de l\'***REMOVED***',
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Younglings::class,
        ]);
    }
}
