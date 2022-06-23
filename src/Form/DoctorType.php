<?php

namespace App\Form;

use App\Entity\Doctor;
use App\Entity\Specialisation;
use App\Enum\RoleEnum;
use App\Form\Base\SelectMultipleType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DoctorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' =>  'Nom'
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre:',
                'placeholder' => 'Choisir un genre: ',
                "constraints" => [new NotBlank()],
                "choices" => [
                    "H" => "H",
                    "F" => "F"
                ]
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
                'label' => 'Compte actif ?',
                'required' => false,
            ])
            ->add('address', AddressType::class, [
                'label' => false
            ])
            ->add('specialisation', EntityType::class, [
                'class' => Specialisation::class,
                'label' => 'Spécialisation:',
                "constraints" => [new NotBlank()],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Doctor::class,
        ]);
    }
}
