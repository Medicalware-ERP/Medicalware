<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\String_;

#[ORM\Entity(repositoryClass: DoctorRepository::class)]
class Doctor extends User
{

    #[ORM\ManyToOne(targetEntity: Specialisation::class, inversedBy: 'doctors')]
    private ?Specialisation $specialisation = null;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: MedicalFileLine::class)]
    private Collection $medicalFileLines;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Patient::class)]
    private Collection $patients;

    public function __construct()
    {
        parent::__construct();
        $this->medicalFileLines = new ArrayCollection();
        $this->patients = new ArrayCollection();
    }

    /**
     * @return Specialisation|null
     */
    public function getSpecialisation(): ?Specialisation
    {
        return $this->specialisation;
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
            $medicalFileLine->setDoctor($this);
        }

        return $this;
    }

    public function removeMedicalFileLine(MedicalFileLine $medicalFileLine): self
    {
        if ($this->medicalFileLines->removeElement($medicalFileLine)) {
            // set the owning side to null (unless already changed)
            if ($medicalFileLine->getDoctor() === $this) {
                $medicalFileLine->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Patient>
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients[] = $patient;
            $patient->setDoctor($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->removeElement($patient)) {
            // set the owning side to null (unless already changed)
            if ($patient->getDoctor() === $this) {
                $patient->setDoctor(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFirstName() . " " . $this->getLastName();
    }

    public function setSpecialisation(?Specialisation $specialisation): self
    {
        $this->specialisation = $specialisation;

        return $this;
    }
}
