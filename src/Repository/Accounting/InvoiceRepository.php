<?php

namespace App\Repository\Accounting;

use App\Entity\Accounting\Invoice;
use App\Repository\Datatable\DatatableConfigJoin;
use App\Repository\Datatable\DatatableConfigSearch;
use App\Repository\Datatable\DatatableRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends DatatableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function add(Invoice $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Invoice $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function configureDatableJoin(): array
    {
        return [
            new DatatableConfigJoin('patient')
        ];
    }

    public function configureDatableSearch(): array
    {
        return [
            new DatatableConfigSearch('reference'),
            new DatatableConfigSearch('firstName', 'patient'),
            new DatatableConfigSearch('lastName', 'patient')
        ];
    }

    public function configureDatableColumns(): array
    {
        return [

        ];
    }

    public function findByDelivery()
    {
        return $this->createQueryBuilder('e')
            ->join('e.state', 'u')
            ->andWhere('u.name = :state')
            ->setParameter('state', "payé")
            ->getQuery()
            ->getResult()
            ;
    }
}
