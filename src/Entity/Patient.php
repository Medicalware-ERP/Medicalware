<?php

namespace App\Entity;

use App\Entity\Accounting\Invoice;
use App\Repository\PatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
class Patient extends Person implements EntityInterface
{
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $numberSocialSecurity = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\Column(type: 'boolean')]
    private bool $isArchived = false;

    #[ORM\OneToOne(inversedBy: "patient", targetEntity: MedicalFile::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?MedicalFile $medicalFile = null;

    #[ORM\Column(type: 'string', length: 5)]
    private ?string $bloodGroup = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class, inversedBy: 'patients')]
    private ?Doctor $doctor = null;

    #[Pure] public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getNumberSocialSecurity(): ?string
    {
        return $this->numberSocialSecurity;
    }

    /**
     * @return MedicalFile|null
     */
    public function getMedicalFile(): ?MedicalFile
    {
        return $this->medicalFile;
    }

    /**
     * @param MedicalFile|null $medicalFile
     * @return Patient
     */
    public function setMedicalFile(?MedicalFile $medicalFile): Patient
    {
        $medicalFile->setPatient($this);
        $this->medicalFile = $medicalFile;
        return $this;
    }

    public function setNumberSocialSecurity(string $numberSocialSecurity): self
    {
        $this->numberSocialSecurity = $numberSocialSecurity;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setPatient($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getPatient() === $this) {
                $invoice->setPatient(null);
            }
        }

        return $this;
    }

    public function isIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getBloodGroup(): ?string
    {
        return $this->bloodGroup;
    }

    public function setBloodGroup(string $bloodGroup): self
    {
        $this->bloodGroup = $bloodGroup;

        return $this;
    }

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }
}
