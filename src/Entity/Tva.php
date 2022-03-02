<?php

namespace App\Entity;

use App\Repository\TvaRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: TvaRepository::class)]
class Tva extends EnumEntity
{

}
