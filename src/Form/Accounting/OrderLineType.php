<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\OrderLine;
use App\Entity\Stock\Equipment;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('equipment', EntityType::class, [
                'class' => Equipment::class,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('e')
                        ->where('e.archivedAt IS NULL');
                },
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionné un équipement')
                ],
                'label' => false,
                'choice_attr' => function(Equipment $choice, $key, $value) {
                    return [
                        'data-provider-id' => $choice->getProvider()->getId()
                    ];
                },
            ])
            ->add('description', TextType::class, [
                'label' => false,
                'required' => false
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

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var OrderLine $orderLine */
            $orderLine = $event->getData();
            if (!$orderLine->getEquipment() instanceof Equipment) {
                return;
            }
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
