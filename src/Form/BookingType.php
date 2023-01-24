<?php

namespace App\Form;

use App\Entity\Bookings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BookingType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void 
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom :",
                "require" => true,
                "constraint" => [new Length(["min" => 5, "max" => "50", "maxMessage" => "Ne pas dépasser 100 caractères."]),
                                 new NotBlank(["message" => "Le nom d'utilisateur ne doit pas être vide !"])]
            ])
            ->add('places', ChoiceType::class, [
                "label" => "Couverts* :",
                "require" => true,
                "choices" => [
                    "1" => 1,
                    "2" => 2,
                    "3" => 3,
                    "4" => 4,
                    "5" => 5,
                    "6" => 6,
                    "7" => 7,
                    "8" => 8,
                    "9" => 9,
                    "10" => 10,
                    "11" => 11,
                    "12" => 12,
                    "13" => 13,
                    "14" => 14,
                    "15" => 15,
                    "16" => 16,
                    "17" => 17,
                    "18" => 18,
                    "19" => 19,
                    "20" => 20,
                ],
                "multiple" => false
            ])
            ->add('allergies', ChoiceType::class, [
                "label" => "Allergie(s) :",
                'choices' => [
                    'Céréales (gluten)' => 'céréales(gluten)',
                    'Céleri' => 'céleri',
                    'Soja' => 'soja',
                    'Lait' => 'lait',
                    'Crustacés' => 'crustacés',
                    'Mollusques' => 'mollusques',
                    'Œuf' => 'œuf',
                    'Moutarde' => 'moutarde',
                    'Poissons' => 'poissons',
                    'Fruit à coque' => 'fruits à coque',
                    'Lupin' => 'lupin',
                    'Graines de sésame' => 'graines de sésame',
                    'Cacahètes' => 'cacahètes',
                    'Dioxyde de souffre(sulfites)' => 'dioxyde de soufre(sulfites)'
                ],
                "multiples" => true,
                "expanded" => true,
                "require" => false,
            ])
            ->add('allergies_other', TextType::class, [
                "label" => "Autre :",
                "require" => false,
            ])
            ->add('date', DateType::class, [
                "label" => "Date",
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') + 5),
                'months' => range(1, 12),
                'days' => range(1, 31),
                'format' => 'dd-MM-yyyy',
                "require" => true,
            ])
            ->add("service", ChoiceType::class, [
                "label" => "Service",
                "choices" => [
                    "Midi" => 0,
                    "Soir" => 1
                ],
                "multiple" => false,
                "require" => true
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Bookings::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'booking_item',
        ]);
    }
}