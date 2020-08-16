<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SelectlongtextType extends AbstractType {

    public function getParent()
    {
        return TextareaType::class;
    }
    
    public function getName()
    {
        return 'selectlongtext';
    }
}