<?php

namespace App\Entity\Room;

use App\Repository\RoomTypeRepository;
use App\Entity\EnumEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomTypeRepository::class)]
class RoomType extends EnumEntity
{
    #[Pure] public function __construct(string $slug = "", string $name = "")
    {
        parent::__construct($slug, $name);
    }

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