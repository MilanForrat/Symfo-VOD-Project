<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignInType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // liste des champs de l'entité
        // Ne pas oublier les use des Types utlisés ci-dessous
            ->add('email', EmailType::class, [
                "label" => "E-mail",
                "attr"=> [
                    "placeholder"=>"Saisir votre e-mail"
                ]
            ])
            ->add('password', PasswordType::class, [
                "label" => "Mot de passe",
                "attr"=> [
                    "placeholder"=>"Saisir votre mot de passe"
                ]
            ])
            ->add('firstName', TextType::class, [
                "label" => "Prénom",
                "attr"=> [
                    "placeholder"=>"Saisir votre prénom"
                ]
            ])
            ->add('LastName', TextType::class, [
                "label" => "Nom",
                "attr"=> [
                    "placeholder"=>"Saisir votre nom"
                ]
            ])
            // ajout la validation du formulaire
            ->add('submit', SubmitType::class, [
                'label'=>'Valider'
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
