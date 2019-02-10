<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    const MODE_CARI_AKUN = 1;
    const MODE_RESET_PASSWORD = 2;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] == self::MODE_CARI_AKUN) {
            $builder
                ->add('identitas', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Username atau email',
                    ]
                ])
            ;
        } else {
            $builder
                ->add('passwordBaru', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Password baru',
                        ],
                    ],
                    'second_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Password baru (ulangi)',
                        ],
                    ],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mode' => null,
        ]);
    }
}
