<?php

namespace App\Form;

use App\Entity\EnumEntity;
use App\Form\Base\EditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                "label" => "Nom :"
            ])
            ->add('color', ColorType::class,[
                "label" => "Couleur :"
            ])
            ->add('description', EditorType::class,[
                "label" => "Description :",
                "label_attr" => [
                    "class" => "d-block margin-bottom-5"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnumEntity::class,
        ]);
    }
}
