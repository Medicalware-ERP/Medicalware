<?php

namespace App\Entity\Planning;

use App\Repository\Planning\ResourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ResourceRepository::class)]
class Resource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups("main")]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Groups("main")]
    private ?int $resourceId = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups("main")]
    private ?string $resourceClass = null;

    private ?object $resource = null;

    #[ORM\OneToMany(mappedBy: 'resource', targetEntity: Event::class, orphanRemoval: true)]
    private $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getResourceName();
    }

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

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setResource($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getResource() === $this) {
                $event->setResource(null);
            }
        }

        return $this;
    }

    /**
     * @return object|null
     */
    public function getResource(): ?object
    {
        return $this->resource;
    }

    /**
     * @param object|null $resource
     */
    public function setResource(?object $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    #[Groups("main")]
    public function getResourceName(): string
    {
        return (string)$this->getResource();
    }
}
