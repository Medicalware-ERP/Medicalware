<?php

namespace App\Entity;

use App\Repository\DiseaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: DiseaseRepository::class)]
class Disease extends EnumEntity
{
    #[ORM\OneToMany(mappedBy: 'disease', targetEntity: MedicalFileLine::class)]
    private Collection $medicalFileLines;

    #[Pure] public function __construct(string $slug, string $name)
    {
        parent::__construct($slug, $name);
        $this->medicalFileLines = new ArrayCollection();
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
            $medicalFileLine->setDisease($this);
        }

        return $this;
    }

    public function removeMedicalFileLine(MedicalFileLine $medicalFileLine): self
    {
        if ($this->medicalFileLines->removeElement($medicalFileLine)) {
            // set the owning side to null (unless already changed)
            if ($medicalFileLine->getDisease() === $this) {
                $medicalFileLine->setDisease(null);
            }
        }

        return $this;
    }
}
