<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public_html function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "label" => "Email :",
                'attr' => ['autocomplete' => 'email'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez remplir ce champ.',
                    ]),
                    new Length([
                        "min"=> 10, "max"=> 255, "minMessage" => "Chaîne de caractères trop courte.", "maxMessage" =>"Chaîne de caractères trop longue."
                    ]),
                    new Email([
                        "message" => "Chaîne de caractères non valide."
                    ])
                ],
            ])
        ;
    }

    public_html function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
