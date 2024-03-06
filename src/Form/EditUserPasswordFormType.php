<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class EditUserPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // pas basé sur l'entity user, donc je définie les règles de validation
            ->add('currentPassword', PasswordType::class,[
                'constraints' => [
                    new UserPassword([
                        "message" => "Le mot de passe actuel est incorrect."
                    ])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le nouveau mot de passe est obligatoire.',
                        ]),
                        new Length([
                            'min' => 12,
                            'max' => 255,
                            'minMessage' => 'Le nouveau mot de passe doit contenir au minimum {{ limit }} characters',
                            'minMessage' => 'Le nouveau mot de passe doit contenir au minimum {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            
                        ]),
                        new Regex([
                            'pattern' => "/^(?=.*[a-zà-ÿ])(?=.*[0-9])(?=.*[^a-zà-ÿA-Z]).{11,255}$/",
                            'match' => true,
                            'message' => "Le mot de passe doit contenir au moins une lettre minuscule un chiffre et caractère"
                        ]),
                        new NotCompromisedPassword([
                            'message' => "Votre mot de passe est facilement piratable! Veuillez en choisir un autre"
                        ])
                    ],
                    
                ],
                
                'invalid_message' => 'Le  nouveau mot de passe doit être identique à sa confirmation.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                // 'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }


}
