<?php

namespace App\Form;

use App\Entity\Template;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class TemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $otpions): void
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Titre :',
            'required' => true,
            'attr' => ['placeholder' => 'Titre...', 'value' => ''],
            'constraints' => [
                new Length(['min' => 2, "max" => 150, "minMessage" => "Chaîne de caractères trop petite.", "maxMessage" => "Chaîne de caractères trop longue."]),
                new Regex([
                    'pattern' => "/^[\p{L}\d\s.\'’()-]+$/u",
                    'message' => "La chaîne de caractères n'est pas valide.",
                ])    
            ]
        ])
        ->add('name', TextType::class, [
            'label' => 'Nom :',
            'required' => true,
            'attr' => ['placeholder' => 'Nom de la réservation', 'value' => ''],
            'constraints' => [
                new Length(['min' => 2, "max" => 100, "minMessage" => "Chaîne de caractères trop petite.", "maxMessage" => "Chaîne de caractères trop longue."]),
                new Regex([
                    'pattern' => "/^[\p{L}\d\s.\'’()-]+$/u",
                    'message' => "La chaîne de caractères n'est pas valide.",
                ])    
            ]
        ])
        ->add('place', NumberType::class, [
            'label' => 'Couverts :',
            'required' => true,
            'attr' => ['placeholder' => '1', 'value' => ''],
            'constraints' => [
                new NotBlank(["message" => "Ne peut pas être vide."]),
                new Type(['type' => 'float', 'message' => 'Doit être un chiffre.']),
                new LessThanOrEqual(['value' => 20, 'message' => 'Contactez directement le restaurant pour des réservations de plus de 20 personnes.']),
                new GreaterThanOrEqual(['value' => 1, 'message' => 'Une personne obligatoirement.'])
                ]
        ])
        ->add('allergies', ChoiceType::class, [
            'label' => 'Allergie(s) :',
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => [
                'Gluten' => 'gluten',
                'Poisson' => 'fish',
                'Crustacés' => 'shellfish',
                'Œufs' => 'eggs',
                'Arachides' => 'peanuts',
                'Moutarde' => 'mustard',
                'Mollusques' => 'molluscs',
                'Soja' => 'soy', 
                'Sulfites' => 'sulphites', 
                'Sésame' => 'sesame',
                'Céleri' => 'celery',
                'Lupins' => 'lupines',
                'Lait' => 'milk',
                'Fruits à coque' => 'nuts'
            ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Template::class
        ]);
    }

}