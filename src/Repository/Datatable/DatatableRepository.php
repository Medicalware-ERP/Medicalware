<?php

namespace App\Repository\Datatable;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use ReflectionException;


abstract class DatatableRepository extends ServiceEntityRepository implements DatatableConfigurationInterface
{
    public const DEFAULT_ENTITY_ALIAS = 'e';
    protected static ?DatatableConfig $datatableConfig = null;

    /**
     * @param int $currentPage
     * @param int $limit
     * @param string|null $query
     * @return Paginator
     * @throws ReflectionException
     */
    public function paginate(int $currentPage, int $limit, string $query = null, \Closure $modifier = null, array $filters = []): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('e');
        $expr = $queryBuilder->expr();


        $this->setJoin($this->configureDatableJoin(), $queryBuilder);

        if ($query) {
            /** @var DatatableConfigSearch $search */
            foreach ($this->configureDatableSearch() as $search) {
                $this->getConfiguration()->addSearch($search);

                $queryBuilder->orWhere($expr->like($search->getAliasJoinField() . '.' . $search->getField(), ':query'));
            }

            $queryBuilder->setParameter('query', '%' . $query . '%');

        }

        /** @var DatatableConfigColumn $column */
        foreach ($this->configureDatableColumns() as $column) {
            $this->getConfiguration()->addColumn($column);

            $queryBuilder->addOrderBy($column->getAliasJoinField() . '.' . $column->getField(), $column->getOrder());
        }

        if($modifier != null){
            $modifier($queryBuilder);
        }

        foreach ($filters as $key => $filter) {
            $field      = $filter['field'];
            $condition  = $filter['condition'];
            $value      =  $filter['value'];
            $queryBuilder->andWhere($field.' '.$condition.' :'.$key);
            $queryBuilder->setParameter($key, $value);
        }

        $queryBuilder
            ->setFirstResult(($currentPage - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($queryBuilder);
    }

    /**
     * @throws ReflectionException
     */
    private function setJoin(iterable $joins , QueryBuilder $queryBuilder) {
        /** @var DatatableConfigJoin $join */
        foreach ($joins as $join) {
            if ($join->getClassName() === null) {
                $this->getConfiguration()->addJoin($join);
            }

            $queryBuilder->leftJoin($join->getAliasClassName().'.'.$join->getField(), $join->getAlias());

            if ($join->getChildren()->count() > 0) {
                $this->setJoin($join->getChildren(), $queryBuilder);
            }
        }
    }

    public function getConfiguration(): DatatableConfig
    {
        if (self::$datatableConfig === null) {
            self::$datatableConfig = new DatatableConfig($this->getClassName());
        }

        return self::$datatableConfig;
    }
}
