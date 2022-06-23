<?php

namespace App\Form;

use App\Entity\Disease;
use App\Entity\Doctor;
use App\Entity\MedicalFileLine;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MedicalFileLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateTimeType::class, [
                'label' => false,
                'widget' => 'single_text',
                'error_bubbling' => true,
                'input_format' => 'yyyy-MM-dd  HH:mm:ss'
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => false,
                'error_bubbling' => true,
                'compound' => false,
                'widget' => "single_text",
                'input_format' => 'yyyy-MM-dd  HH:mm:ss'
            ])
            ->add('doctor', EntityType::class, [
                'label' => false,
                'class' => Doctor::class,
                "constraints" => [new NotBlank()],
            ])
            ->add('service', EntityType::class, [
                'label' => false,
                'class' => Service::class,
                "constraints" => [new NotBlank()],
            ])
            ->add('disease', EntityType::class, [
                'label' => false,
                'class' => Disease::class,
                "constraints" => [new NotBlank()],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MedicalFileLine::class,
        ]);
    }
}
