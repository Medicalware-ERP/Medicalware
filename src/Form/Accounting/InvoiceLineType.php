<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\InvoiceLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => false
            ])
            ->add('description', TextType::class, [
                'label' => false
            ])
            ->add('quantity', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'x-model.number' => 'qty'
                ]
            ])
            ->add('price', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'x-model.number' => 'price'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceLine::class,
        ]);
    }
}
