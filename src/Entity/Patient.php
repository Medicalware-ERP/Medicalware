<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use App\Service\DataFormatterInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
class Patient extends Person implements EntityInterface
{
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $numberSocialSecurity = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;

    public function getNumberSocialSecurity(): ?string
    {
        return $this->numberSocialSecurity;
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
}
