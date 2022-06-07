<?php

namespace App\Entity;

use App\Repository\RoomTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomTypeRepository::class)]
class RoomType extends EnumEntity
{

}
