<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\TvaRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: TvaRepository::class)]
class Tva extends EnumEntity
{

}
