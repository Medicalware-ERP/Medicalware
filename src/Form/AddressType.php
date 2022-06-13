<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street', TextType::class, [
                'label' => 'Rue',
                "constraints" => [new NotBlank(message: 'Veuillez remplir ce champ')],
            ])
            ->add('complementaryInfo', TextType::class, [
                'label' => 'Info complémentaire',
                'required' => false
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                "constraints" => [new NotBlank(message: 'Veuillez remplir ce champ')],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                "constraints" => [new NotBlank(message: 'Veuillez remplir ce champ')],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'label' => false
        ]);
    }
}
