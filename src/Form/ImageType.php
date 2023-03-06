<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', FileType::class, [
                "label" => "Image :",
                "required" => true,
                "constraints" => [
                    new File([
                        'maxSize' => '5M',
                        'maxSizeMessage' => "L'image est trop lourde.",
                        "mimeTypes" => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => "Format d'image non valide."
                    ])
                ]
            ])
            ->add('title', TextType::class, [
                "label" => "Titre :",
                "required" => true,
                'attr' => ['placeholder' => 'Titre...', 'value' => ''],
                "constraints" => [new Length(['min' => 2, "max" => 50, "minMessage" => "Titre trop petit.", "maxMessage" => "Titre trop long."])]
        ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            "csrf_field_name" => "_token",
            'csrf_token_id' => "post_item"
        ]);
    }
}