<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CariAkunType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identitas', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Username atau email',
                ]
            ])
        ;
    }
}
