<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RestorePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code',IntegerType::class,[
                'attr' => [
                    'placeholder'=>'Code',
                    'class'=>'form-control'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes ne sont pas identiques.',
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder'=> 'Nouveau mot de passe',
                        'class'=>'form-control'
                    ]
                ],
                'required' => true,
                'first_options'  => [
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir {{ limit }} caractéres',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ]
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
