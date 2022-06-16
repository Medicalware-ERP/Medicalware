<?php

namespace App\Repository;

use App\Entity\Room\Room;
use App\Repository\Datatable\DatatableConfigSearch;
use App\Repository\Datatable\DatatableConfigJoin;
use App\Repository\Datatable\DatatableRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends DatatableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Room $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllActive()
    {
        return $this->findBy([
            "archivedAt" => null
        ]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Room $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function configureDatableColumns(): array
    {
        return [];
    }

    #[Pure] public function configureDatableJoin(): array
    {
        return [
            new DatatableConfigJoin('type')
        ];
    }

    #[Pure] public function configureDatableSearch(): array
    {
        return [
            new DatatableConfigSearch('label'),
            new DatatableConfigSearch('capacity'),
            new DatatableConfigSearch('name', 'type'),
        ];
    }
}
