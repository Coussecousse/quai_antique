<?php


namespace App\Form;

use App\Entity\Menu;
use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class MenusType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
                'label' => 'Nom :',
                'required' => true,
                'attr' => ['placeholder' => 'Nom du menu...', 'value' => ''],
                'constraints' => [
                    new Length(["min" => 2, "max" => 150,"minMessage" => "Chaîne de caractères trop petite.", "maxMessage" => "Chaîne de caractères trop longue."]),
                    new Regex([
                        'pattern' => "/^[\p{L}\d\s.\'’()-]+$/u",
                        'message' => "La chaîne de caractères n'est pas valide.",
                    ]),
                    new NotBlank(["message" => "Ne peut pas être vide."]),
                ]  
            ])
        ->add('offers', CollectionType::class, [
            'entry_type' => OfferType::class,
            'label' => false,
            'entry_options' => ['label' => false],
            'required' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Menu::class
        ]);
    }
}