<?php

namespace App\Form;

use App\Entity\Date;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DatesType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       $builder 
        ->add('date', DateType::class, [
            "label" => 'Date : ',
            "widget" => "single_text",
            'required' => true,
            'input' => 'datetime'
        ])
        ->add('evening_start', TimeType::class, [
            'required' => false,
            "label" => "Heure de début de service",
            "widget" => "single_text",
            'input' => 'datetime'
        ])
        ->add('evening_end', TimeType::class, [
            'required' => false,
            "label" => "Heure de fin de service :",
            "widget" => "single_text",
            'input' => 'datetime'
        ])
        ->add('evening_close', CheckboxType::class, [
            'required' => false,
            "label" => "Fermer"
        ])
        ->add('evening_normal', CheckboxType::class, [
            'required' => false,
            "label" => "Horaires habituels"
        ])
        ->add('noon_start', TimeType::class, [
            'required' => false,
            "label" => "Heure de début de service",
            "widget" => "single_text",
            'input' => 'datetime'
        ])
        ->add('noon_end', TimeType::class, [
            'required' => false,
            "label" => "Heure de fin de service :",
            "widget" => "single_text",
            'input' => 'datetime'
        ])
        ->add('noon_close', CheckboxType::class, [
            'required' => false,
            "label" => "Fermer"
        ])
        ->add('noon_normal', CheckboxType::class, [
            'required' => false,
            "label" => "Horaires habituels"
        ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            
        ]);
    }
}