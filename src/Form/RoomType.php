<?php

namespace App\Form;

use App\Entity\Room\Room;
use App\Entity\Room\RoomOption;
use App\Form\Base\SelectMultipleType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\Routing\Annotation\getOptions;

class RoomType extends AbstractType
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $roomOptions = $this->manager->getRepository(RoomOption::class)
            ->createQueryBuilder("e")
            ->andWhere("e.archivedAt is null")
            ->getQuery()
            ->getResult();

        $roomOptions = array_unique(array_merge($builder->getData()->getOptions()->toArray(), $roomOptions));

        $roomTypes = $this->manager->getRepository(\App\Entity\Room\RoomType::class)
            ->createQueryBuilder("e")
            ->andWhere("e.archivedAt is null")
            ->getQuery()
            ->getResult();

        $roomTypes[] = $builder->getData()->getType();
        $roomTypes = array_unique($roomTypes);

        $builder
            ->add('label', TextType::class, [
                'label' =>  'Libellé'
            ])
            ->add('capacity', NumberType::class, [
                'label' =>  'Capacité'
            ])
            ->add('options', EntityType::class, [
                "class" => RoomOption::class,
                "multiple" => true,
                "choices" => $roomOptions,
                "required" => false
            ])
            ->add('type', EntityType::class, [
                "class" => \App\Entity\Room\RoomType::class,
                "choices" => $roomTypes
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class
        ]);
    }
}
