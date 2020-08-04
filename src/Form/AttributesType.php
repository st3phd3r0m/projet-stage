<?php

namespace App\Form;

use App\Entity\AttributeGroups;
use App\Entity\Attributes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Form\Type\DatalistType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AttributesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('attribute_group', EntityType::class, [
                'required' => false,
                'label' => 'Choisir le groupe d\'attributs',
                'class' => AttributeGroups::class,
                'choice_label' => 'name',
            ]);


        if($options['embeddedToProductForm'] == false){
            //Cas où l'utilisateur édite/crée un attribut en dehors du formulaire ProductsType
            $builder
                ->add('name', TextType::class, [
                    'required' => true,
                    'label' => 'Nom de l\'attribut : ',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un nom d\'attribut.',
                        ])
                    ]
                ])
                ->add('value', TextareaType::class, [
                    'required' => true,
                    'label' => 'Contenu de l\'attribut : ',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir le contenu de l\'attribut',
                        ])
                    ]
                ]);

        }else{
            //Cas où l'utilisateur édite/crée un attribut lors de l'édition/création d'un produit
            $builder
                ->add('name', DatalistType::class,[
                    'required' => false,
                    'label' => 'Nom de l\'attribut : ',
                ])
                ->add('value', DatalistType::class,[
                    'required' => false,
                    'label' => 'Contenu de l\'attribut : ',
                ]);
            //Ajout d'un ecouteur d'événements pour modifier l'option 'mapped'=>true en 'mapped'=>false
            //sur les champs name et value de attribut lors de la soumission du formulaire    
            $builder->addEventListener(
                FormEvents::PRE_SUBMIT,
                function(FormEvent $event) {
                    $form = $event->getForm();

                    $attributeGroupsFieldOptions = $form->get('attribute_group')->getConfig()->getOptions();
                    $attributeGroupsFieldOptions['mapped'] = false;
                    $form->add('attribute_group', EntityType::class, $attributeGroupsFieldOptions);

                    $nameFieldOptions = $form->get('name')->getConfig()->getOptions();
                    $nameFieldOptions['mapped'] = false;
                    $form->add('name', DatalistType::class, $nameFieldOptions);

                    $valueFieldOptions = $form->get('value')->getConfig()->getOptions();
                    $valueFieldOptions['mapped'] = false;
                    $form->add('value', DatalistType::class, $valueFieldOptions);
                }
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attributes::class,
            'embeddedToProductForm'=>true
        ]);
    }
}
