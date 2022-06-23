<?php

namespace App\Form;

use App\Entity\Accounting\Invoice;
use App\Entity\Accounting\InvoiceLine;
use App\Entity\MedicalFile;
use App\Entity\MedicalFileLine;
use Doctrine\Common\Collections\Collection;
use Faker\Provider\Medical;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MedicalFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('medicalFileLines', CollectionType::class, [
                'entry_type' => MedicalFileLineType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MedicalFile::class,
        ]);
    }
}
