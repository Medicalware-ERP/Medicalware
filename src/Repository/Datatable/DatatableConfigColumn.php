<?php

declare(strict_types=1);

namespace App\Repository\Datatable;


use JetBrains\PhpStorm\Pure;

class DatatableConfigColumn extends DatatableConfigField
{

    #[Pure]
    public function __construct(
        string         $field,
        private string $order = 'asc',
        string         $aliasJoinField = DatatableRepository::DEFAULT_ENTITY_ALIAS
    )
    {
        parent::__construct($field, $aliasJoinField);
    }


    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $order
     * @return DatatableConfigColumn
     */
    public function setOrder(string $order): DatatableConfigColumn
    {
        $this->order = $order;
        return $this;
    }
}