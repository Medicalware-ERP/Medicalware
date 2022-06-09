<?php

namespace App\Entity;

use App\Entity\Stock\Equipment;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(targetEntity: ServiceSpecialisation::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ServiceSpecialisation $serviceSpecialisation = null;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: MedicalFileLine::class)]
    private Collection $medicalFileLines;

    #[ORM\ManyToMany(targetEntity: Equipment::class, mappedBy: 'service')]
    private $equipment;

    #[Pure] public function __construct()
    {
        $this->medicalFileLines = new ArrayCollection();
        $this->equipment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getServiceSpecialisation(): ?ServiceSpecialisation
    {
        return $this->serviceSpecialisation;
    }

    public function setServiceSpecialisation(ServiceSpecialisation $serviceSpecialisation): self
    {
        $this->serviceSpecialisation = $serviceSpecialisation;

        return $this;
    }

    /**
     * @return Collection<int, MedicalFileLine>
     */
    public function getMedicalFileLines(): Collection
    {
        return $this->medicalFileLines;
    }

    public function addMedicalFileLine(MedicalFileLine $medicalFileLine): self
    {
        if (!$this->medicalFileLines->contains($medicalFileLine)) {
            $this->medicalFileLines[] = $medicalFileLine;
            $medicalFileLine->setService($this);
        }

        return $this;
    }

    public function removeMedicalFileLine(MedicalFileLine $medicalFileLine): self
    {
        if ($this->medicalFileLines->removeElement($medicalFileLine)) {
            // set the owning side to null (unless already changed)
            if ($medicalFileLine->getService() === $this) {
                $medicalFileLine->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment[] = $equipment;
            $equipment->addService($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipment->removeElement($equipment)) {
            $equipment->removeService($this);
        }

        return $this;
    }
}
