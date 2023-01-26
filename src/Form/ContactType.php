<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom :",
                "required" => true,
                "constraints" => [
                    new Length(["min" => 5, "max" => 100, "minMessage" => "Chaîne de caractères trop petite.", "maxMessage" => "Chaîne de caractères trop longue."]),
                    new NotBlank(["message" => "Ne peut pas être vide."]),
                ]
            ])
            ->add('tel', TelType::class, [
                "label" => "Tel :",
                "required" => true,
                "constraints" => [
                    new NotBlank(["message" => "Ne peut pas être vide."]), 
                    new Regex([
                        'pattern' => '/^[0-9\+\]{8,11}$/',
                        'message' => 'Veuillez rentrer un numéro de téléphone valide.',
                    ]),
                ]
            ])
            ->add('message', TextareaType::class, [
                "label" => "Message :", 
                "required" => true,
                "constraints" => [
                    new Length(["min"=> 20, "max" => 450, "minMessage" => "Message trop court.", "maxMessage"=> "Message trop long."]),
                    new NotBlank(["message" => "Ne peut pas être vide."])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
