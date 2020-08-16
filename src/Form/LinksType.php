<?php

namespace App\Form;

use App\Entity\Links;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LinksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', ChoiceType::class,[
                'label'=>'Où voulez-vous placer le lien ?',
                'choices'=>[
                    'Bas de page (footer)'=>'footer',
                    'Haut de page (header)'=>'header',
                    'Barre de navigation (haut de page)'=>'nav',
                    'Dropdown dans la barre de navigation (haut de page)'=>'dropdown',
                ],
                'constraints' => [
					new NotBlank([
						'message' => 'Veuillez saisir une position pour le lien'
					])
				]
            ])
            ->add('position_order', NumberType::class,[
                'required'=>true,
                'label'=>'Ordre d\'apparition du lien ?',
                'html5'=>true,
                'constraints' => [
					new NotBlank([
						'message' => 'Veuillez saisir un ordre d\'apparition'
					])
				]
            ])
            ->add('type', ChoiceType::class,[
                'required' => true,
                'label'=>'Type de lien à ajouter',
                'choices'=>[
                    'Acceuil'=>'/acceuil',
                    'Qui sommes nous'=>'/qui-sommes-nous',
                    'Petits éclaireurs'=>'/equipe-petits-eclaireurs',
                    'Foire aux questions'=>'/faq',
                    'Sorties thématiques'=>'/***REMOVED***-thematiques',
                    'Toutes les ***REMOVED***'=>'/toutes-les-***REMOVED***',
                    'Page de publication'=>'/page/',
                    'Catégorie'=>'/categorie/',
                    'Lien sortant'=>'external',
                ],
                'data' => '/acceuil',
                'constraints' => [
					new NotBlank([
						'message' => 'Veuillez choisir un type de lien'
					])
				]
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre du lien : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ])
                ]
            ])
            ->add('content', TextType::class, [  
                'required' => true,
                'label'=>'Contenu du lien : ',
                'constraints' => [
                    new NotBlank([
                        'message'=>'Veuillez saisir le contenu du lien',
                    ]),
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Choisir une icône (optionnel)',
                'download_uri' => false,
                'imagine_pattern' => 'miniatures',
                'constraints' => [
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
            'data_class' => Links::class,
        ]);
    }
}
