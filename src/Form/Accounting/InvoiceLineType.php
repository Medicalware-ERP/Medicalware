<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\InvoiceLine;
use App\Form\Base\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

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
                    'x-model.number' => 'qty',
                    'min' => 0
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir une quantité'),
                    new Range(minMessage: 'Veuillez saisir une valeur supérieur ou égale à 0', min: 0),
                ],
            ])
            ->add('price', FloatType::class, [
                'label' => false,
                'attr' => [
                    'x-model.number' => 'price',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un prix'),
                    new Range(minMessage: 'Veuillez saisir une valeur supérieur ou égale à 0', min: 0),
                ],
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
