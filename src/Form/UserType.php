<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "label" => "Email :",
                "required" => true
            ])
            ->add('password', PasswordType::class, [
                "label" => "Mot de passe :",
                "required" => true
            ])
            ->add('confirm', PasswordType::class, [
                "label" => "Confirmer le mot de passe :",
                "required" => true,
                "constraints" => [
                    new NotBlank(['message' => "Veuillez remplir ce champ."]),
                    new Callback(['callback' => function ($value, ExecutionContext $ec) 
                    {
                        if ($ec->getRoot()['password']->getViewData() !== $value) 
                        {
                            $ec->addViolation("Les mots de passe ne sont pas identiques.");
                        }
                    }])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
