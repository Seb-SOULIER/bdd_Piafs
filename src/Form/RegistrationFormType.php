<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname',TextType::class,[
                'attr'=>[
                    'placeholder'=>'Nom',
                    'class'=>'form-control'
                ]
            ])
            ->add('firstname',TextType::class,[
                'attr'=>[
                    'placeholder'=>'Prénom',
                    'class'=>'form-control'
                ]
            ])
            ->add('email',EmailType::class,[
                'attr' => [
                    'placeholder'=>'Email',
                    'class'=>'form-control'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utlisation.',
                    ]),
                ],
            ])
            //->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
            //    'mapped' => false,
            //    'attr' => [
            //        'autocomplete' => 'new-password',
            //        'placeholder'=> 'Mot de passe',
            //        'class'=>'form-control'],
            //    'constraints' => [
            //        new NotBlank([
            //            'message' => 'Saisir un mot de passe',
            //        ]),
            //        new Length([
            //            'min' => 6,
            //            'minMessage' => 'Votre mot de passe doit contenir {{ limit }} caractéres',
            //            // max length allowed by Symfony for security reasons
            //            'max' => 4096,
            //        ]),
            //    ],
            //])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes ne sont pas identiques.',
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder'=> 'Mot de passe',
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
            'data_class' => User::class,
        ]);
    }
}
