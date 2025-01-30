<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Video;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'label'=> 'Mot de passe actuel',
                "attr"=> [
                    "placeholder"=>"Saisir votre mot de passe actuel"
                ],
                'mapped'=>false, 
            ])
            ->add('plainPassword', RepeatedType::class, [
                "type"=> PasswordType::class,
                'constraints'=>[
                    new Length([
                        'min'=>4,
                        'max'=>30
                    ])
                ],
            'first_options' => [
                "label" => "Nouveau mot de passe",
                "attr"=> [
                    "placeholder"=>"Saisir votre nouveau mot de passe"
                ],
                'hash_property_path'=>'password'
            ],
            'second_options'=>[
                "label" => "Confirmez le nouveau mot de passe",
                "attr"=> [
                    "placeholder"=>"Confirmez votre nouveau mot de passe"
                ]
            ],
            'mapped'=>false, 
        ])
        // ajout la validation du formulaire
        ->add('submit', SubmitType::class, [
            'label'=>'Valider',
            "attr"=> [
                    "class"=>"btn btn-success w-100"
                ],
        ])
        ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event){
            $form = $event->getForm();
            $user = $form->getConfig()->getOptions()['data'];
            $passwordHasher=$form->getConfig()->getOptions()['passwordHasher'];
           
            $isValid = $passwordHasher->isPasswordValid(
                $user,
                $form->get('actualPassword')->getData()
            );
            if(!$isValid){
                $form->get('actualPassword')->addError(new FormError("votre mot de passe actuel ne correspond pas à celui de votre compte."));
            }
        })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null
        ]);
    }
}
