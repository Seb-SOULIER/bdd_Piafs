<?php

namespace App\Form;

use App\Entity\Atelier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AtelierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'attr'=>[
                    'style'=>'width:90%'
                ]
            ])
            ->add('date',DateType::class,[
                'widget' => 'single_text',
                'attr'=>[
                    'style'=> 'width:90%'
                ]
            ])
            ->add('hourStart',TimeType::class,[
                'widget' => 'single_text',
                'attr'=>[
                    'style'=> 'width:90%'
                ]
            ])
            ->add('hourStop',TimeType::class,[
                'widget' => 'single_text',
                'attr'=>[
                    'style'=> 'width:90%'
                ]
            ])
            ->add('place',NumberType::class,[
                'attr'=>[
                    'style'=> 'width:90%'
                ]
            ])
            ->add('description',TextareaType::class,[
                'attr' => [
                    'rows'=>'4',
                    'style'=> 'width:90%'
                ]
            ])
            // ->add('placeReserved')
            // ->add('intervenant')
            // ->add('participants')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Atelier::class,
        ]);
    }
}
