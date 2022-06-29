<?php

namespace App\Repository\Planning;

use App\Entity\Planning\Event;
use App\Entity\Planning\Participant;
use App\Entity\Planning\Resource;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use function App\Entity\getId;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $manager)
    {
        parent::__construct($registry, Event::class);
    }

    public function findAllFromResourcesAndAttendees()
    {
        $resourceRepository = $this->manager->getRepository(Resource::class);
        $events = $this->findAll();
        $eventsActive = [];

        /** @var Event $event */
        foreach ($events as $event) {
            $eventsActive[] = $event;
            $attendees = $event->getAttendees();

            /** @var Participant $attendee */
            foreach ($attendees as $attendee)
            {
                // Si la ressource de l'évènement a le même id et la même class que l'un des attendees, on ne l'ajoute pas
                if ($attendee->getResourceId() == $event->getResource()->getResourceId() &&
                $attendee->getResourceClass() == $event->getResource()->getResourceClass())
                {
                    continue;
                }

                $newEvent = $event->copyEvent();

                $resource = $resourceRepository->findOneBy(["resourceId" => $attendee->getResourceId(), "resourceClass" => $attendee->getResourceClass()]);
                $newEvent->setResource($resource);

                $eventsActive[] = $newEvent;
            }
        }

        return $eventsActive;
    }

    public function findEventsBy(Resource $resource)
    {
        $events = $this->findAll();
        $eventsActive = [];

        /** @var Event $event */
        foreach ($events as $event) {
            if ($event->getResource() == $resource)
            {
                $eventsActive[] = $event;
                continue;
            }

            $attendees = $event->getAttendees();

            /** @var Participant $attendee */
            foreach ($attendees as $attendee)
            {
                // Si la ressource de l'évènement a le même id et la même class que les params, on l'ajoute
                if ($attendee->getResourceId() == $resource->getResourceId() &&
                    $attendee->getResourceClass() == $resource->getResourceClass())
                {
                    $newEvent = $event->copyEvent();
                    $newEvent->setResource($resource);

                    $eventsActive[] = $newEvent;
                    break;
                }
            }
        }

        return $eventsActive;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Event $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Event $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findEventOfTodayByUser($userId)
    {
        $startAt = (new DateTime())->setTime(00, 00 , 01, 00);
        $endAt = (new DateTime())->setTime(23, 59 , 59, 59);

        return $this->createQueryBuilder('e')
            ->leftJoin('e.attendees', 'p')
            ->join('e.resource', 'r')
            ->orWhere('p.resourceId = :userId')
            ->orWhere('r.resourceId = :userId')
            ->andWhere('e.endAt >= :startAt AND e.startAt <= :endAt')
            ->setParameter('startAt', $startAt)
            ->setParameter('endAt', $endAt)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
