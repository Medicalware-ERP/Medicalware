<?php

namespace App\Entity\Planning;

use App\Entity\EnumEntity;
use App\Repository\Planning\EventTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventTypeRepository::class)]
class EventType extends EnumEntity
{

}
