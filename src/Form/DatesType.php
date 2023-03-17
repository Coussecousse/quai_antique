<?php

namespace App\Form;

use App\Entity\Date;
use Doctrine\DBAL\Types\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DatesType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       $builder 
        ->add('date', DateType::class, [
            "label" => 'Date : ',
            'attr' => ['placeholder' => [
                'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
            ]],
            'required' => true,
            'input' => 'datetime'
        ])
        ->add('evening_start', TimeType::class, [
            "label" => "Heure de début de service",
            'attr' => ['placeholder' => [
                'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde',]],
            'input' => 'datetime'
        ])
        ->add('evening_end', TimeType::class, [
            "label" => "Heure de fin de service :",
            'attr' => ['placeholder' => [
                'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde',]],
            'input' => 'datetime'
        ])
        ->add('evening_close', CheckboxType::class, [
            "label" => "Fermer"
        ])
        ->add('evening_normal', CheckboxType::class, [
            "label" => "Comme d'habitude"
        ])
        ->add('noon_start', TimeType::class, [
            "label" => "Heure de début de service",
            'attr' => ['placeholder' => [
                'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde',]],
            'input' => 'datetime'
        ])
        ->add('noon_end', TimeType::class, [
            "label" => "Heure de fin de service :",
            'attr' => ['placeholder' => [
                'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde',]],
            'input' => 'datetime'
        ])
        ->add('noon_close', CheckboxType::class, [
            "label" => "Fermer"
        ])
        ->add('noon_normal', CheckboxType::class, [
            "label" => "Comme d'habitude"
        ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Date::class
        ]);
    }
}