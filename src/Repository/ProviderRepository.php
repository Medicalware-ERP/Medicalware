<?php

namespace App\Repository;

use App\Entity\Provider;
use App\Repository\Datatable\DatatableConfigColumn;
use App\Repository\Datatable\DatatableConfigSearch;
use App\Repository\Datatable\DatatableRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Provider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Provider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Provider[]    findAll()
 * @method Provider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderRepository extends DatatableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function add(Provider $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Provider $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function configureDatableJoin(): array
    {
        return [];
    }

    public function configureDatableSearch(): array
    {
        return [
            new DatatableConfigSearch('name'),
            new DatatableConfigSearch('phone'),
            new DatatableConfigSearch('email'),
        ];
    }

    public function configureDatableColumns(): array
    {
        return [
            new DatatableConfigColumn('name'),
        ];
    }
}
