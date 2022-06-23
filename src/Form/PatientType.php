<?php

namespace App\Form;

use App\Entity\Patient;
use App\Form\Base\PhoneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' =>  'Nom',
                'attr' => array(
                    'placeholder' => 'Nom'
                ),
                "constraints" => [new NotBlank()]
            ])
            ->add('firstName', TextType::class, [
                'label' =>  'Prénom',
                'attr' => array(
                    'placeholder' => 'Prénom'
                ),
                "constraints" => [new NotBlank()]
            ])
            ->add('phoneNumber', PhoneType::class, [
                'attr' => array(
                    'placeholder' => 'Ex: 06 18 61 51 28'
                ),
            ])
            ->add('birthdayDate', DateType::class, [
                'label' => 'Date de naissance',
                "constraints" => [new NotBlank()],
                'widget' => 'single_text'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => array(
                    'placeholder' => 'Ex: example@example.com'
                ),
                "constraints" => [new NotBlank()]
            ])
            ->add('address', AddressType::class, [
                'label' => false,
                "constraints" => [new NotBlank()]
            ])
            ->add('numberSocialSecurity', TextType::class, [
                'label' => 'Numéro de sécurité sociale',
                'attr' => array(
                    'placeholder' => 'Ex: 1 74 39 67 053 855 60'
                ),
                "constraints" => [new NotBlank()],
                "help" => "Votre numéro doit faire 15 chiffres."
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre:',
                'placeholder' => 'Choisir un genre: ',
                "constraints" => [new NotBlank()],
                "choices" => [
                    "M" => "M",
                    "F" => "F"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
