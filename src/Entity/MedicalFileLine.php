<?php

namespace App\Entity;

use App\Repository\MedicalFileLineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Range;

#[ORM\Entity(repositoryClass: MedicalFileLineRepository::class)]
class MedicalFileLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: MedicalFile::class, inversedBy: 'medicalFileLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MedicalFile $medicalFile = null;

    #[ORM\ManyToOne(targetEntity: Doctor::class, inversedBy: 'medicalFileLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Doctor $doctor = null;

    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'medicalFileLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

    #[ORM\ManyToOne(targetEntity: Disease::class, inversedBy: 'medicalFileLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Disease $disease = null;

    #[ORM\Column(type: 'datetime')]
    #[Range(maxMessage: "La date de début: {{ value }} doit être inférieur à la date de fin", maxPropertyPath: "endDate")]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: 'datetime')]
    #[Range(minMessage: "La date de fin: {{ value }} doit être supérieur à la date de début", minPropertyPath: "startDate")]
    private ?\DateTimeInterface $endDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedicalFile(): ?MedicalFile
    {
        return $this->medicalFile;
    }

    public function setMedicalFile(?MedicalFile $medicalFile): self
    {
        $this->medicalFile = $medicalFile;

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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getDisease(): ?Disease
    {
        return $this->disease;
    }

    public function setDisease(?Disease $disease): self
    {
        $this->disease = $disease;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }
}
