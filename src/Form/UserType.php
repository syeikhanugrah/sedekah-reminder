<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    const MODE_EDIT_PROFILE = 1;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('namaLengkap')
            ->add('username')
            ->add('email')
            ->add('nomorHp')
            ->add('roles', ChoiceType::class, [
                'label' => 'Peran',
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Password (ulangi)'],
            ])
        ;

        if ($options['mode'] === self::MODE_EDIT_PROFILE) {
            $builder->remove('roles');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'mode' => null,
        ]);
    }
}
