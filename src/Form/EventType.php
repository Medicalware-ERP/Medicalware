<?php

namespace App\Form;

use App\Entity\Planning\Event;
use App\Entity\Planning\Participant;
use App\Form\Base\EditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                "label" => "Titre"
            ])
            ->add('type', EntityType::class, [
                "label" => "Type",
                "class" => \App\Entity\Planning\EventType::class
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => "Date début",
                'widget' => 'single_text',
                'input_format' => 'yyyy-MM-dd  HH:mm:ss'
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => "Date fin",
                'widget' => 'single_text',
                'input_format' => 'yyyy-MM-dd  HH:mm:ss'
            ])
            ->add('description', EditorType::class,[
                "label" => "Description :",
                "label_attr" => [
                    "class" => "d-block margin-bottom-5"
                ]
            ])
            ->add('color', ColorType::class,[
                "label" => "Couleur"
            ])
            ->add('attendees', EntityType::class, [
                "label" => "Participants",
                "class" => Participant::class,
                "multiple" => true,
                "required" => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
