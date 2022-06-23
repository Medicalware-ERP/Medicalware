<?php

namespace App\Repository\Accounting;

use App\Entity\Accounting\Order;
use App\Entity\Accounting\OrderLine;
use App\Entity\Stock\Equipment;
use App\Repository\Datatable\DatatableConfigJoin;
use App\Repository\Datatable\DatatableConfigSearch;
use App\Repository\Datatable\DatatableRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends DatatableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function add(Order $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Order $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function configureDatableJoin(): array
    {
        $orderLines = new DatatableConfigJoin('orderLines');
        $orderLines
            ->addChildren(new DatatableConfigJoin('equipment', className: OrderLine::class));

        return [
            new DatatableConfigJoin('provider'),
            $orderLines
        ];
    }

    public function configureDatableSearch(): array
    {
        return [
            new DatatableConfigSearch('name', 'provider'),
            new DatatableConfigSearch('reference'),
        ];
    }

    public function configureDatableColumns(): array
    {
        return [

        ];
    }
}
