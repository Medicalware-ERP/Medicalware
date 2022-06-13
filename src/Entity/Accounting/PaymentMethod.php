<?php

namespace App\Entity\Accounting;

use App\Entity\EnumEntity;
use App\Repository\Accounting\PaymentMethodRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentMethodRepository::class)]
class PaymentMethod extends EnumEntity
{
}
