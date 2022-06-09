<?php

namespace App\Entity;

use App\Repository\SpecialisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: SpecialisationRepository::class)]
class Specialisation extends EnumEntity
{
    #[ORM\ManyToMany(targetEntity: Doctor::class, mappedBy: 'specialisations')]
    private Collection $doctors;

    #[Pure] public function __construct(string $slug, string $name)
    {
        parent::__construct( $slug,  $name);
        $this->doctors = new ArrayCollection();
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
            $doctor->addSpecialisation($this);
        }

        return $this;
    }

    public function removeDoctor(Doctor $doctor): self
    {
        if ($this->doctors->removeElement($doctor)) {
            $doctor->removeSpecialisation($this);
        }

        return $this;
    }
}
