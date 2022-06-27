<?php

namespace App\Entity;

use App\Entity\Stock\Equipment;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service extends EnumEntity
{
    #[ORM\OneToMany(mappedBy: 'service', targetEntity: MedicalFileLine::class, orphanRemoval: "CASCADE")]
    private Collection $medicalFileLines;

    #[ORM\ManyToMany(targetEntity: Equipment::class, mappedBy: 'services')]
    private Collection $equipments;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Doctor::class)]
    private Collection $doctors;

    #[Pure] public function __construct(string $slug = "", string $name = "")
    {
        parent::__construct($slug, $name);
        $this->medicalFileLines = new ArrayCollection();
        $this->equipments = new ArrayCollection();
        $this->doctors = new ArrayCollection();
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
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
            $equipment->addService($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipments->removeElement($equipment)) {
            $equipment->removeService($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Doctor>
     */
    public function getDoctors(): Collection
    {
        return $this->doctors;
    }

    public function addDoctor(Doctor $doctor): self
    {
        if (!$this->doctors->contains($doctor)) {
            $this->doctors[] = $doctor;
            $doctor->setService($this);
        }

        return $this;
    }

    public function removeDoctor(Doctor $doctor): self
    {
        if ($this->doctors->removeElement($doctor)) {
            // set the owning side to null (unless already changed)
            if ($doctor->getService() === $this) {
                $doctor->setService(null);
            }
        }

        return $this;
    }
}
