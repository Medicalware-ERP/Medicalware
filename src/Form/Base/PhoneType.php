<?php

namespace App\Form\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PhoneType extends AbstractType
{
    public function getParent(): string
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' =>  'Téléphone',
            "constraints" => [
                new NotBlank([
                    "message" => "Le champs ne doit être pas vide",
                ]),
                new Regex('/^((\+|00)33\s?|0)[67](\s?\d{2}){4}$/', "Le format du numéro n'est pas bon.")
            ]
        ]);
    }
}