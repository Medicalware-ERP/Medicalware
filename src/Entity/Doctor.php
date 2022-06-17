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

    #[ORM\ManyToMany(targetEntity: Specialisation::class, inversedBy: 'doctors')]
    private Collection $specialisations;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: MedicalFileLine::class)]
    private Collection $medicalFileLines;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Patient::class)]
    private Collection $patients;

    public function __construct()
    {
        parent::__construct();
        $this->specialisations = new ArrayCollection();
        $this->medicalFileLines = new ArrayCollection();
        $this->patients = new ArrayCollection();
    }

    /**
     * @return Collection<int, Specialisation>
     */
    public function getSpecialisation(): Collection
    {
        return $this->specialisations;
    }

    public function addSpecialisation(Specialisation $specialisation): self
    {
        if (!$this->specialisations->contains($specialisation)) {
            $this->specialisations[] = $specialisation;
        }

        return $this;
    }

    public function removeSpecialisation(Specialisation $specialisation): self
    {
        $this->specialisations->removeElement($specialisation);

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
}
