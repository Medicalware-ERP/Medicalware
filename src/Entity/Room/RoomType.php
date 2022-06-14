<?php

namespace App\Entity\Room;

use App\Entity\EnumEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomTypeRepository::class)]
class RoomType extends EnumEntity
{

}