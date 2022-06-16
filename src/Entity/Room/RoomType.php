<?php

namespace App\Entity\Room;

use App\Repository\RoomTypeRepository;
use App\Entity\EnumEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomTypeRepository::class)]
class RoomType extends EnumEntity
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $archivedAt;

    public function getArchivedAt(): ?\DateTimeImmutable
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeImmutable $archivedAt): self
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }
}