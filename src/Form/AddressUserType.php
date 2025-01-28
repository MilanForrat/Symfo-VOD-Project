<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label'=>'Votre Prénom',
                'attr'=>[
                    'placeholder'=>'Indiquez votre prénom...'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label'=>'Votre Nom',
                'attr'=>[
                    'placeholder'=>'Indiquez votre nom...'
                ]
            ])
            ->add('enterprise', TextType::class, [
                'label'=>'Votre Société',
                'required'   => false,
                'attr'=>[
                    'placeholder'=>'Indiquez votre société (facultatif)'
                ]
            ])
            ->add('address', TextType::class, [
                'label'=>'Votre Adresse',
                'attr'=>[
                    'placeholder'=>'Indiquez votre adresse...'
                ]
            ])
            ->add('postCode', TextType::class, [
                'label'=>'Votre Code Postal',
                'attr'=>[
                    'placeholder'=>'Votre code postal...'
                ]
            ])
            ->add('city', TextType::class, [
                'label'=>'Votre Ville',
                'attr'=>[
                    'placeholder'=>'Indiquez votre ville...'
                ]
            ])
            ->add('country', CountryType::class, [
                'label'=>'Votre Pays',
                'attr'=>[
                    'placeholder'=>'Indiquez votre pays...'
                ]
            ])
            ->add('phone', TextType::class, [
                'label'=>'Votre Numéro de Téléphone',
                'attr'=>[
                    'placeholder'=>'Indiquez votre numéro de téléphone...'
                ]
            ])
            // ajout la validation du formulaire
            ->add('submit', SubmitType::class, [
                'label'=>'Enregistrer la nouvelle adresse',
                'attr'=>[
                    'class'=>'btn btn-success mt-5'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
