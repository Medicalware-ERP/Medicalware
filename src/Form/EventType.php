<?php

namespace App\Form;

use App\Entity\Patient;
use App\Entity\Planning\Event;
use App\Entity\Planning\Participant;
use App\Form\Base\SelectMultipleType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\Base\EditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = $this->manager->getRepository(User::class)->findAll();
        $choices = array_merge($choices, $this->manager->getRepository(Patient::class)->findAll());

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
            ->add('attendees', SelectMultipleType::class, [
                "label" => "Participants",
                "required" => false,
                "choices" => $choices,
                "mapped" => false,
                "choice_label" => function($a) { return $a; }
            ])
            ->add('allDay', CheckboxType::class, [
                "label" => "Toute la journée",
                "required" => false
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $formEvents) {
            /** @var Event $event */
            $event = $formEvents->getData();
            $event->setColor($event->getType()->getColor());

            /** @var \DateTime $startAt */
            $startAt = $event->getStartAt();

            /** @var \DateTime $endAt */
            $endAt = $event->getEndAt();

            // Si les deux jours sont different, on set le allDay à true (pour le resize dans calendar)
            $event->setAllDay($startAt->format("d") != $endAt->format("d") || $event->getAllDay());

            if ($event->getAllDay())
            {
                $event->setStartAt($startAt->setTime(0, 0 ,0));
                $event->setEndAt($endAt->setTime(0, 0 ,0)->modify("+1 day"));
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $formEvents) {
            /** @var Event $event */
            $event = $formEvents->getData();
            $formData = $formEvents->getForm();

            foreach ($formData->get("attendees")->getData() as $participant) {
                $attendee = new Participant();
                $attendee->setResourceId($participant->getId());
                $attendee->setResourceClass($participant::class);

                $event->addAttendee($attendee);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
