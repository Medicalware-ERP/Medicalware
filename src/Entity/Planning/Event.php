<?php

namespace App\Entity\Planning;

use App\Repository\Planning\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
    private ?\DateTimeInterface $startAt = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups("main")]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\Column(type: 'integer')]
    #[Groups("main")]
    private ?int $resourceId = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups("main")]
    private ?string $resourceClass = null;

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

    public function __construct()
    {
        $this->attendees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
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
}
