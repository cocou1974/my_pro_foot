<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('firstName', TextType::class)
            ->add('lastName',  TextType::class)
            ->add('email',     EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe doit être identique à sa confirmation.',
                // 'options' => ['attr' => ['class' => 'password-field']],
                // 'required' => true,
                // 'first_options'  => ['label' => 'Password'],
                // 'second_options' => ['label' => 'Repeat Password'],
            ])
            // ->add('plainPassword',  PasswordType::class,
            // ->add('password',  RepeatedType::class, [
                // 'type' => PasswordType::class,
                // 'invalid_message' => 'Le mot de passe doit être identique à sa confirmation.',
            // ])
           
            // ->add('plainPassword', PasswordType::class, [
                                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                // 'mapped' => false,
                // 'attr' => ['autocomplete' => 'new-password'],
                // 'constraints' => [
                    // new NotBlank([
                        // 'message' => 'Please enter a password',
                    // ]),
                    // new Length([
                        // 'min' => 6,
                        // 'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        // 'max' => 4096,
                    // ]),
                // ],
            // ])


            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                     new IsTrue([
                         'message' => 'Veuillez acceptez les condition générales d\'utilisation.',
                    ]),
                ],
            ]);
         }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
