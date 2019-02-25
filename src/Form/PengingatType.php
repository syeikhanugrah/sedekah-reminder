<?php

namespace App\Form;

use App\Entity\Pengingat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PengingatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('judul')
            ->add('tanggalAwal', DateType::class, [
                'label' => 'Tanggal dimulai',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker-tanggalAwal',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('tanggalAkhir', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker-tanggalAkhir',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('selamanya', CheckboxType::class, [
                'required' => false,
                'label' => 'Buat pengingat ini selamanya',
            ])
            ->add('perulangan', ChoiceType::class, [
                'choices' => array_flip(Pengingat::getDaftarPerulangan()),
            ])
            ->add('mingguanHariKe', ChoiceType::class, [
                'required' => false,
                'choices' => array_flip(Pengingat::getDaftarNamaHari()),
                'placeholder' => 'Pilih hari',
            ])
            ->add('bulananHariKe', ChoiceType::class, [
                'required' => false,
                // Array tidak di-flip karna key dan value-nya sama
                'choices' => Pengingat::getDaftarAngkaHariSebulan(),
                'placeholder' => 'Pilih tanggal',
            ])
            ->add('nominalSedekah', MoneyType::class, [
                'currency' => 'IDR',
                'grouping' => 3,
                'scale' => 0,
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
        ;

        if ($options['isAdmin']) {
            $builder
                ->add('namaPenerima', TextType::class, [
                    'required' => true,
                    'data' => $options['namaPenerima'],
                    'mapped' => !$options['isPemilikPengingat'],
                    'attr' => [
                        'placeholder' => 'Untuk siapa pengingat ini?',
                    ],
                ])
                ->add('nomorHpPenerima', TextType::class, [
                    'required' => true,
                    'data' => $options['nomorHpPenerima'],
                    'mapped' => !$options['isPemilikPengingat'],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('isAdmin');

        $resolver->setDefaults([
            'data_class' => Pengingat::class,
            'isPemilikPengingat' => null,
            'namaPenerima' => null,
            'nomorHpPenerima' => null,
        ]);
    }
}
