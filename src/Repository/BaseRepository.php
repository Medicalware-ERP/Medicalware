<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;


abstract class BaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAllPaginated(int $currentPage, int $limit, string $query = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->join('u.profession', 'ut')
            ->andWhere('u.lastName LIKE :query OR u.firstName LIKE :query OR u.email LIKE :query OR u.phoneNumber LIKE :query OR ut.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setFirstResult(($currentPage - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy("u.lastName", "asc");

        return new Paginator($queryBuilder);
    }
}
