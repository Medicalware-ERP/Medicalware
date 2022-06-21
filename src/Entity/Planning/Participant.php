<?php

namespace App\Entity\Planning;

use App\Repository\Planning\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $resourceId = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $resourceClass = null;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'attendees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResourceId(): ?int
    {
        return $this->resourceId;
    }

    public function setResourceId(int $resourceId): self
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    public function getResourceClass(): ?string
    {
        return $this->resourceClass;
    }

    public function setResourceClass(string $resourceClass): self
    {
        $this->resourceClass = $resourceClass;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }
}
