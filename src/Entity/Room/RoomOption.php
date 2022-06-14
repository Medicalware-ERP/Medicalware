<?php

namespace App\Entity\Room;

use App\Entity\EnumEntity;
use App\Repository\RoomOptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomOptionRepository::class)]
class RoomOption extends EnumEntity
{

}
