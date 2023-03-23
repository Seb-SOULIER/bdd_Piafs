<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('lastname',TextType::class,[
                'attr'=>[
                    'style'=> 'width:90%'
                ]
            ])
            ->add('firstname',TextType::class,[
                'attr'=>[
                    'style'=> 'width:90%'
                ]
            ])
            ->add('birthdate',DateType::class,[
                'widget' => 'single_text',
                'attr'=>[
                    'style'=> 'width:90%'
                ]
            ])
            // ->add('avatar')
            ->add('address',TextareaType::class,[
                'attr' => [
                    'rows'=>'2',
                    'style'=> 'width:90%'
                ]
            ])
            ->add('zipcode',NumberType::class,[
                'attr'=>[
                    'style'=> 'width:90%'
                ]
            ])
            ->add('city',TextType::class,[
                'attr'=>[
                    'style'=>'width:90%'
                ]
            ])
            ->add('phone',TextType::class,[
                'attr'=>[
                    'style'=>'width:90%'
                ]
            ])
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
