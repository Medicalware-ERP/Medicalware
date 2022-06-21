<?php

namespace App\Form\Stock;

use App\Entity\Provider;
use App\Entity\Stock\Equipment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Prix'
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('services')
            ->add('provider', EntityType::class, [
                'class' => Provider::class,
                'label' => 'Fournisseur'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipment::class,
        ]);
    }
}
