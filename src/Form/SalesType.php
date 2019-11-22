<?php

namespace App\Form;

use App\Entity\Sales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oyunid')
            ->add('category')
            ->add('description')
            ->add('price')
            ->add('username')
            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sales::class,
            'csrf_protection'=> false,
        ]);
    }
}
