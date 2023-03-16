<?php

namespace App\Form;

use App\Entity\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('start', TimeType::class, [
            'label' => 'Début :',
            'input' => 'datetime',
            'required' => false,
            'attr' => ['placeholder' => 'hh:mm']
        ])
        ->add('end', TimeType::class, [
            'label' => 'Fin :',
            'input' => 'datetime',
            'required' => false,
            'attr' => ['placeholder' => 'hh:mm']
        ])
        ->add('close', CheckboxType::class, [
            'label' => 'Fermé',
            
        ])
        ->add('day', HiddenType::class, [
            'required' => true,
        ])
        ->add('time', HiddenType::class, [
            'required' => true,
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void 
    {
        $resolver->setDefaults([
            // 'data_class' => Schedule::class,
        ]);
    }
}