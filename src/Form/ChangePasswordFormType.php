<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordFormType extends AbstractType
{
    public_html function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez remplir ce champ.',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Chaîne de caractères trop courte.',
                            // max length allowed by Symfony for security reasons
                            'max' => 60,
                        ]),
                        new Regex([
                            "pattern" => "/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/", "message" => "Le mot de passe doit posséder au minimum 8 caractères, une lettre majuscule, une lettre minuscule et un chiffre."
                        ])
                    ],
                    'label' => 'Nouveau mot de passe :',
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe :',
                ],
                'invalid_message' => 'Les mots de passes ne sont pas identiques.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public_html function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
