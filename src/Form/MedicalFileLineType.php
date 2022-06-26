<?php

namespace App\Form;

use App\Entity\Disease;
use App\Entity\Doctor;
use App\Entity\MedicalFileLine;
use App\Entity\Service;
use PhpParser\Comment\Doc;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                'choice_attr' => function(Doctor $choice, $key, $value) {
                    return [
                        'data-service-name' => $choice->getService()?->getName()
                    ];
                },
                'attr' => [
                    'class' => "doctor__service"
                ]
            ])
            ->add('disease', EntityType::class, [
                'label' => false,
                'class' => Disease::class,
                "constraints" => [new NotBlank()],
            ])
            ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
            /** @var  MedicalFileLine $medicalFileLine */
            $medicalFileLine = $event->getData();
            $medicalFileLine->setService($medicalFileLine->getDoctor()->getService());
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MedicalFileLine::class,
        ]);
    }
}
