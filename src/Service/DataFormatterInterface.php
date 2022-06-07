<?php

namespace App\Service;

use App\Entity\EntityInterface;

interface DataFormatterInterface
{
    public function format(EntityInterface $data): array;
}