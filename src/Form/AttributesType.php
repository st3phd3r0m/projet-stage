<?php

namespace App\Form;

use App\Entity\AttributeGroups;
use App\Entity\Attributes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Form\Type\SelecttextType;
use App\Form\Type\SelectlongtextType;
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
                ->add('name', SelecttextType::class,[
                    'required' => false,
                    'label' => 'Nom de l\'attribut : ',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un nom d\'attribut.',
                        ])
                    ]
                ])
                ->add('value', SelectlongtextType::class,[
                    'required' => false,
                    'label' => 'Contenu de l\'attribut : ',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir le contenu de l\'attribut',
                        ])
                    ],
                ]);

        }else{
            //Cas où l'utilisateur édite/crée un attribut lors de l'édition/création d'un produit
            $builder
                ->add('name', SelecttextType::class,[
                    'required' => false,
                    'label' => 'Nom de l\'attribut : ',
                ])
                ->add('value', SelectlongtextType::class,[
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
                    $form->add('name', SelecttextType::class, $nameFieldOptions);

                    $valueFieldOptions = $form->get('value')->getConfig()->getOptions();
                    $valueFieldOptions['mapped'] = false;
                    $form->add('value', SelectlongtextType::class, $valueFieldOptions);
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
