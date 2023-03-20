<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('email')
            // ->add('roles')
            // ->add('password')
            // ->add('token')
            // ->add('validToken')
            ->add('lastname')
            ->add('firstname')
            ->add('birthdate')
            // ->add('avatar')
            ->add('address')
            ->add('zipcode')
            ->add('city')
            ->add('phone')
            // ->add('subcribeAt')
            // ->add('restoreCode')
            // ->add('allActif')
            // ->add('allInactif')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
