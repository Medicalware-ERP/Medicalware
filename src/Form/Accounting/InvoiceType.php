<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\Invoice;
use App\Entity\Accounting\InvoiceLine;
use App\Entity\Accounting\PaymentMethod;
use App\Entity\Patient;
use App\Entity\Tva;
use App\Form\Base\EditorType;
use Doctrine\Common\Collections\Collection;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference')
            ->add('date', DateType::class, [
                'label' => 'Date',
                "constraints" => [new NotBlank()],
                'widget' => 'single_text'
            ])
            ->add('comment', EditorType::class, [
                'label' => 'Commentaire'
            ])
            ->add('patient', EntityType::class, [
                'class' => Patient::class,
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
            ->add('invoiceLines', CollectionType::class, [
                'entry_type' => InvoiceLineType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Invoice $invoice */
            $invoice      =  $event->getData();
            $invoice->calculate();
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
