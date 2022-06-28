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
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
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
        $choices = $this->manager->getRepository(User::class)->findAllActive();
        $choices = array_merge($choices, $this->manager->getRepository(Patient::class)->findAllActive());

        $builder
            ->add('title', TextType::class,[
                "label" => "Titre",
                "required" => true
            ])
            ->add('type', EntityType::class, [
                "label" => "Type",
                "class" => \App\Entity\Planning\EventType::class,
                "required" => true
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => "Date début",
                'widget' => 'single_text',
                'input_format' => 'yyyy-MM-dd  HH:mm:ss',
                "required" => true
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => "Date fin",
                'widget' => 'single_text',
                'input_format' => 'yyyy-MM-dd  HH:mm:ss',
                "required" => true
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

            // Si le allDay est vrai, on set le temps à minuit et +1j pour le dernier jour
            if ($event->getAllDay())
            {
                $event->setStartAt($startAt->setTime(0, 0 ,0));
                $event->setEndAt($endAt->setTime(0, 0 ,0)->modify("+1 day"));
            }
        });


        $builder->get('attendees')
            ->addModelTransformer(
                new class implements DataTransformerInterface{

                    public function transform(mixed $value): array
                    {
                        /** @var Participant[] $participants */
                        $participants = $value->toArray();

                        $data = [];
                        foreach ($participants as $participant) {
                            $data[] = $participant->getResource();
                        }

                        return $data;
                    }

                    public function reverseTransform(mixed $value): array
                    {
                        $data = [];

                        foreach ($value as $item) {
                            $id = $item->getId();

                            $data[] = (new Participant())
                                        ->setResourceId($id)
                                        ->setResourceClass($item::class)
                            ;
                        }

                        return $data;
                    }
                }
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
