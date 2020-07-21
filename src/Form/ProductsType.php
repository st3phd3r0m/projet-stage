<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('hangout_location')
            ->add('keywords')
            ->add('image')
            ->add('meta_tag_title')
            ->add('meta_tag_description')
            ->add('meta_tag_keywords')
            ->add('reference')
            ->add('weezeevent')
            ->add('created_at')
            ->add('updated_at')
            ->add('pre_tax_price')
            ->add('tax_included_price')
            ->add('slug')
            ->add('language')
            ->add('category')
            ->add('attribute')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
