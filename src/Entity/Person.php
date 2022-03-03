<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Repository\PersonRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $firstName = null;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\Column(type: 'string', length: 14)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $birthdayDate = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Person
     */
    public function setId(?int $id): Person
    {
        $this->id = $id;
        return $this;
    }


    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBirthdayDate(): ?DateTimeImmutable
    {
        return $this->birthdayDate;
    }

    public function setBirthdayDate(DateTimeImmutable $birthdayDate): self
    {
        $this->birthdayDate = $birthdayDate;

        return $this;
    }
}
