<?php

namespace App\Form;

use App\Entity\Children;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildrenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'attr'=>[
                    'style'=>'width:90%'
                ]
            ])
            ->add('birthdate',DateType::class,[
                'widget' => 'single_text',
                'attr'=>[
                    'style'=> 'width:90%'
                ]
            ])
            // ->add('isActive')
            // ->add('activeAt')
            ->add('firstname',TextType::class,[
                'attr'=>[
                    'style'=>'width:90%'
                ]
            ])
            // ->add('parent')
            // ->add('ateliers')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Children::class,
        ]);
    }
}
