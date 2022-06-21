<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\OrderLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('equipment')
            ->add('description')
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

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var OrderLine $orderLine */
            $orderLine = $event->getData();

            $orderLine->setEquipmentName($orderLine->getEquipment()->getName());
            $orderLine->setEquipmentReference($orderLine->getEquipment()->getReference());
            $orderLine->setEquipmentPrice($orderLine->getEquipment()->getPrice());
            $orderLine->calculateHt();
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderLine::class,
        ]);
    }
}
