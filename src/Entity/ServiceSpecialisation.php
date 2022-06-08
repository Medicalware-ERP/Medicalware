<?php

namespace App\Entity;

use App\Repository\ServiceSpecialisationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceSpecialisationRepository::class)]
class ServiceSpecialisation extends EnumEntity
{

}
