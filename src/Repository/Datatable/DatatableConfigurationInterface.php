<?php

namespace App\Repository\Datatable;

interface DatatableConfigurationInterface
{
    public function configureDatableJoin(): array;
    public function configureDatableSearch(): array;
    public function configureDatableColumns(): array;
}