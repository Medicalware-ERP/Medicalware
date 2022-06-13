<?php

namespace App\Entity\Accounting;

use App\Entity\EnumEntity;
use App\Repository\Accounting\InvoiceStateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceStateRepository::class)]
class InvoiceState extends EnumEntity
{

}