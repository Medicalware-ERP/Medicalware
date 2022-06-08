<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
class Patient extends Person
{
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $numberSocialSecurity = null;


    public function getNumberSocialSecurity(): ?string
    {
        return $this->numberSocialSecurity;
    }

    public function setNumberSocialSecurity(string $numberSocialSecurity): self
    {
        $this->numberSocialSecurity = $numberSocialSecurity;

        return $this;
    }
}
