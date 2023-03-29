<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class CardType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
            ->add('title', TextType::class, [
                "label" => "Titre :",
                'attr' => ['placeholder' => 'Titre...', 'value' => ''],
                "required" => true,
                "constraints" => [
                    new Length(["min" => 2, "max" => 150,"minMessage" => "Chaîne de caractères trop petite.", "maxMessage" => "Chaîne de caractères trop longue."]),
                    new Regex([
                        'pattern' => "/^[\p{L}\d\s.\'’()-]+$/u",
                        'message' => "La chaîne de caractères n'est pas valide.",
                    ]),
                    new NotBlank(["message" => "Ne peut pas être vide."]),
                ]
            ])
            ->add('description', TextType::class, [
                "label" => "Description :",
                "attr" => ['placeholder' => "Description...", 'value' => ''],
                "required" => false,
                "constraints" => [
                    new Length(["min" => 0, "max" => 255,"minMessage" => "Chaîne de caractères trop petite.", "maxMessage" => "Chaîne de caractères trop longue."]),
                    new Regex([
                        'pattern' => "/^[\p{L}\d\s.,()'’-]{0,255}$/u",
                        'message' => "La chaîne de caractères n'est pas valide.",
                    ])
                ]
            ])
            ->add('price', NumberType::class, [
                'html5' => true,
                "label" => "Prix :",
                "scale" => 2,
                "attr" => ['placeholder'=> "Prix...", "value" => ''],
                "required" => true,
                "constraints" => [
                    new NotBlank(["message" => "Ne peut pas être vide."]),
                    new Type(['type' => 'float', 'message' => 'Doit être un chiffre.'])
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
