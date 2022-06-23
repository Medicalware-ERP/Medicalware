<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\Invoice;
use App\Entity\Accounting\InvoiceLine;
use App\Entity\Accounting\Order;
use App\Entity\Accounting\PaymentMethod;
use App\Entity\Provider;
use App\Entity\Tva;
use App\Form\Base\EditorType;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => 'Reference',
                "constraints" => [new NotBlank()],
            ])
            ->add('provider', EntityType::class, [
                'class' => Provider::class,
                'label' => 'Fournisseur',
                "constraints" => [new NotBlank()],
            ])
            ->add('paymentMethod', EntityType::class, [
                'class' => PaymentMethod::class,
                'label' => 'Méthode de paiement',
                "constraints" => [new NotBlank()],
            ])
            ->add('tva', EntityType::class, [
                'class' => Tva::class,
                'label' => 'Tva',
                "constraints" => [new NotBlank()],
            ])
            ->add('deliveryDate', DateType::class, [
                'label' => 'Date de livraison',
                'widget' => 'single_text'
            ])
            ->add('deliveryPlannedDate', DateType::class, [
                'label' => 'Date prévue',
                'widget' => 'single_text'
            ])
            ->add('comment', EditorType::class, [
                'label' => 'Commentaire'
            ])
            ->add('orderLines', CollectionType::class, [
                'entry_type' => OrderLineType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Order $order */
            $order = $event->getData();

            $order->calculate();
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
