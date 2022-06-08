<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\Translation\t;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $street = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $complementaryInfo = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $postalCode = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $city = null;

    public function __construct(string $street = null, string $city = null, string $postalCode = null)
    {
        $this->street = $street;
        $this->city  = $city;
        $this->postalCode = $postalCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getComplementaryInfo(): ?string
    {
        return $this->complementaryInfo;
    }

    public function setComplementaryInfo(?string $complementaryInfo): self
    {
        $this->complementaryInfo = $complementaryInfo;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getStreet().' '.$this->getComplementaryInfo().' '.$this->getCity().' '.$this->getPostalCode();
    }
}
