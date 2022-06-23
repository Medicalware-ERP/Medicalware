<?php

namespace App\Repository\Stock;

use App\Entity\Stock\Equipment;
use App\Entity\Stock\Stock;
use App\Repository\Datatable\DatatableConfigJoin;
use App\Repository\Datatable\DatatableConfigSearch;
use App\Repository\Datatable\DatatableRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use ReflectionException;

/**
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends DatatableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

     public function add(Stock $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

     public function remove(Stock $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ReflectionException
     */
    public function configureDatableJoin(): array
    {
        $equipment = new DatatableConfigJoin('equipment');

        $equipment->addChildren(new DatatableConfigJoin('provider', className: Equipment::class));
        $equipment->addChildren(new DatatableConfigJoin('services', className: Equipment::class));

        return [
            $equipment
        ];
    }

    public function configureDatableSearch(): array
    {
        return [
            new DatatableConfigSearch('name', 'provider'),
            new DatatableConfigSearch('name', 'equipment'),
            new DatatableConfigSearch('reference', 'equipment'),
            new DatatableConfigSearch('name', 'services'),
        ];
    }

    public function configureDatableColumns(): array
    {
        return [

        ];
    }
}
