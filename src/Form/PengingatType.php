<?php

namespace App\Form;

use App\Entity\Pengingat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pengingat::class,
        ]);
    }
}
