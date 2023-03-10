<?php


namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class OfferType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Nom :',
            'required' => true,
            'attr' => ['placeholder' => 'Nom de la formule...', 'value' => ''],
            'constraints' => [
                new Length(["min" => 2, "max" => 150,"minMessage" => "Chaîne de caractères trop petite.", "maxMessage" => "Chaîne de caractères trop longue."]),
                new Regex([
                    'pattern' => "/^[\p{L}\d\s.\'’()-]+$/u",
                    'message' => "La chaîne de caractères n'est pas valide.",
                ]),
                new NotBlank(["message" => "Ne peut pas être vide."]),
            ],
        ])
        ->add('conditions', TextType::class, [
            'label' => 'Condition :',
            'attr' => ['placeholder' => 'Condition(s)...', 'value' => ''],
            'required' => false,
            'constraints' => [
                new Length(["min" => 0, "max" => 255,"minMessage" => "Chaîne de caractères trop petite.", "maxMessage" => "Chaîne de caractères trop longue."]),
                new Regex([
                    'pattern' => "/^[\p{L}\d\s.\'’()-]+$/u",
                    'message' => "La chaîne de caractères n'est pas valide.",
                ]),
            ]
        ])
        ->add('description', CollectionType::class, [
            'label' => 'Composer de : ',
            'attr' => ['placeholder' => 'Composer de...', 'value' => ''],
            'entry_type' => TextType::class,
            'allow_add' => true, 
            'allow_delete' => true,
        ])
        ->add('price', NumberType::class, [
            "label" => "Prix :",
            "scale" => 2,
            "attr" => ['placeholder'=> "Prix...", "value" => ''],
            "required" => true,
            "constraints" => [
                new NotBlank(["message" => "Ne peut pas être vide."]),
            ]
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
            // Configure your form options here
        ]);
    }
}