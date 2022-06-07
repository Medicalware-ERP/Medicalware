<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;

// TODO : Rajouter liaison au service

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: 'integer')]
    private ?int $capacity = null;

    #[ORM\ManyToOne(targetEntity: RoomType::class, inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RoomType $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getType(): ?RoomType
    {
        return $this->type;
    }

    public function setType(?RoomType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
