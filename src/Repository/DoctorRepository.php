<?php

namespace App\Repository;

use App\Entity\Doctor;
use App\Repository\Datatable\DatatableConfigColumn;
use App\Repository\Datatable\DatatableConfigJoin;
use App\Repository\Datatable\DatatableConfigSearch;
use App\Repository\Datatable\DatatableRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\Pure;

/**
 * @method Doctor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Doctor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Doctor[]    findAll()
 * @method Doctor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctorRepository extends DatatableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Doctor::class);
    }

    /**
     */
    public function add(Doctor $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     */
    public function remove(Doctor $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    #[Pure] public function configureDatableJoin(): array
    {
        return [
            new DatatableConfigJoin('specialisation'),
        ];
    }

    #[Pure] public function configureDatableSearch(): array
    {
        return [
            new DatatableConfigSearch('lastName'),
            new DatatableConfigSearch('phoneNumber'),
            new DatatableConfigSearch('email'),
            new DatatableConfigSearch('firstName'),
            new DatatableConfigSearch('name','specialisation'),
        ];
    }

    #[Pure] public function configureDatableColumns(): array
    {
        return [
            new DatatableConfigColumn('lastName'),
        ];
    }

    // /**
    //  * @return Doctor[] Returns an array of Doctor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Doctor
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
