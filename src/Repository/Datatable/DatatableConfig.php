<?php

declare(strict_types=1);

namespace App\Repository\Datatable;

use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\Pure;
use phpDocumentor\Reflection\Types\This;
use ReflectionException;


class DatatableConfig
{
    #[Pure]
    public function __construct(
        private string          $className,
        private ArrayCollection $joins = new ArrayCollection(),
        private ArrayCollection $searches = new ArrayCollection(),
        private ArrayCollection $columns = new ArrayCollection(),
    )
    {
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     * @return DatatableConfig
     */
    public function setClassName(string $className): DatatableConfig
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @return ArrayCollection<DatatableConfigJoin>
     */
    public function getJoins(): ArrayCollection
    {
        return $this->joins;
    }

    /**
     * @throws ReflectionException
     */
    public function addJoin(DatatableConfigJoin $join)
    {
        if (!$this->joins->contains($join)) {
            $join->setDatatableConfig($this);
            $join->setClassName($this->className);
            if ($join->isValidJoin()) {
                $this->joins->add($join);
            }
        }
    }

    /**
     * @return ArrayCollection<DatatableConfigSearch>
     */
    public function getSearches(): ArrayCollection
    {
        return $this->searches;
    }

    /**
     * @throws ReflectionException
     */
    public function addSearch(DatatableConfigSearch $configSearch) {
        if (!$this->searches->contains($configSearch)) {
            $configSearch->setDatatableConfig($this);
            if ($configSearch->isFieldValid()) {
                $this->searches->add($configSearch);
            }
        }
    }

    /**
     * @return ArrayCollection<DatatableConfigColumn>
     */
    public function getColumns(): ArrayCollection
    {
        return $this->columns;
    }

    /**
     * @throws ReflectionException
     */
    public function addColumn(DatatableConfigColumn $configColumn) {
        if (!$this->columns->contains($configColumn)) {
            $configColumn->setDatatableConfig($this);
            if ($configColumn->isFieldValid()) {
                $this->columns->add($configColumn);
            }
        }
    }

}