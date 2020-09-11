<?php

namespace App\Form;

use App\Entity\Links;
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
        // dd($options['data']->getType());

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
                'label'=>'Ordre d\'apparition du lien ? (Saisir un chiffre. Du plus petit au plus grand : liens de la gauche vers la droite)',
                'html5'=>true,
                'constraints' => [
					new NotBlank([
						'message' => 'Veuillez saisir un nombre entier'
					])
				]
            ])
            ->add('type', ChoiceType::class,[
                'required' => true,
                'label'=>'Type de lien à ajouter',
                'choices'=>[
                    'Accueil (page éditable dans le menu "Gestion des pages")'=>'/accueil',
                    'Qui sommes nous (page éditable dans le menu "Gestion des pages")'=>'/qui-sommes-nous',
                    ' Beta testers(page éditable dans le menu "Gestion des pages")'=>'/equipe-petits-eclaireurs',
                    'Foire aux questions (page éditable dans le menu "Gestion des pages")'=>'/foire-aux-questions',
                    ' Produits thématiques(page éditable dans le menu "Gestion des pages")'=>'/***REMOVED***-thematiques',
                    ' Tous les produits(page éditable dans le menu "Gestion des pages")'=>'/toutes-les-***REMOVED***',
                    'Page de publication'=>'/page/',
                    'Catégorie'=>'/categorie/',
                    'Lien sortant'=>'external',
                ],
                'data' => $options['data']->getType(), //'/accueil',
                'constraints' => [
					new NotBlank([
						'message' => 'Veuillez choisir un type de lien'
					])
				]
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre du lien (Visible lors du survol de la souris sur le lien) : ',

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre.',
                    ])
                ]
            ])
            ->add('content', TextType::class, [  
                'required' => true,
                'label'=>'Texte du lien (Contenu directement visible à l\'écran) : ',
                'constraints' => [
                    new NotBlank([
                        'message'=>'Veuillez saisir le contenu du lien',
                    ]),
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Choisir une icône (optionnel, n\'apparaîtra que pour les liens au haut à gauche de la page )',
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
