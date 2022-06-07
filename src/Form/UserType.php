<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public const FORM_NAME  = 'user';
    public const FORM_ID    = 'test';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' =>  'Nom'
            ])
            ->add('firstName', TextType::class, [
                'label' =>  'Prénom'
            ])
            ->add('phoneNumber', TextType::class, [
                'label' =>  'Téléphone'
            ])
            ->add('birthdayDate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Compte actif ?'
            ])
            ->add('address', AddressType::class, [
                'label' => false
            ])
            ->add('profession')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'form_name'  => self::FORM_NAME,
            'form_id'    => self::FORM_ID,
        ]);
    }
}
