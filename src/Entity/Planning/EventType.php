<?php

namespace App\Entity\Planning;

use App\Entity\EnumEntity;
use App\Repository\Planning\EventTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventTypeRepository::class)]
class EventType extends EnumEntity
{
    #[Pure] public function __construct(string $slug = "", string $name = "", $description = "", $color = "#FFFFF")
    {
    parent::__construct($slug, $name, $description, $color);
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $archivedAt;


    public function getArchivedAt(): ?\DateTimeInterface
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeInterface $archivedAt): self
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }
}
