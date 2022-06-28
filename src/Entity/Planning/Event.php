<?php

namespace App\Entity\Planning;

use App\Repository\Planning\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Range;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups("main")]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups("main")]
    private ?string $title = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups("main")]
    #[Range(maxMessage: "La date de début doit être inférieur à la date de fin", maxPropertyPath: "endAt")]
    private ?\DateTimeInterface $startAt = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups("main")]
    #[Range(minMessage: "La date de fin doit être supérieur à la date de début", minPropertyPath: "startAt")]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups("main")]
    private ?string $color = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Participant::class, orphanRemoval: true, cascade: ["persist", "remove"] )]
    #[Groups("main")]
    private Collection $attendees;

    #[ORM\ManyToOne(targetEntity: EventType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("main")]
    private $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups("main")]
    private $description;

    #[ORM\Column(type: 'boolean')]
    #[Groups("main")]
    private bool $allDay = false;

    #[ORM\ManyToOne(targetEntity: Resource::class, inversedBy: 'events', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("main")]
    private $resource;

    public function __construct()
    {
        $this->attendees = new ArrayCollection();
    }

    // Utiliser dans le cas de la duplication d'un évènement pour l'affichage sur la ressource et les attendees dans le planning
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<int, Participant>
     */
    public function getAttendees(): \Doctrine\Common\Collections\Collection
    {
        return $this->attendees;
    }

    public function addAttendee(Participant $attendee): self
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees[] = $attendee;
            $attendee->setEvent($this);
        }

        return $this;
    }

    public function removeAttendee(Participant $attendee): self
    {
        if ($this->attendees->removeElement($attendee)) {
            // set the owning side to null (unless already changed)
            if ($attendee->getEvent() === $this) {
                $attendee->setEvent(null);
            }
        }

        return $this;
    }

    public function getType(): ?EventType
    {
        return $this->type;
    }

    public function setType(?EventType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function setAllDay(bool $allDay): self
    {
        $this->allDay = $allDay;

        return $this;
    }

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function copyEvent(): Event
    {
        $newEvent = new Event();
        $newEvent->setId($this->getId());
        $newEvent->setTitle($this->getTitle());
        $newEvent->setType($this->getType());
        $newEvent->setDescription($this->getDescription());
        $newEvent->setAllDay($this->getAllDay());
        $newEvent->setStartAt($this->getStartAt());
        $newEvent->setEndAt($this->getEndAt());
        $newEvent->setColor($this->getColor());
        $newEvent->setResource($this->getResource());

        return $newEvent;
    }
}
