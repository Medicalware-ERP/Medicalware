<?php

namespace App\Repository;

use App\Entity\ServiceSpecialisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceSpecialisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceSpecialisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceSpecialisation[]    findAll()
 * @method ServiceSpecialisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceSpecialisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceSpecialisation::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ServiceSpecialisation $entity, bool $flush = true): void
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
    public function remove(ServiceSpecialisation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return ServiceSpecialisation[] Returns an array of ServiceSpecialisation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ServiceSpecialisation
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
