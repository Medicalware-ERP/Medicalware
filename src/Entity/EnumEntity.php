<?php

namespace App\Entity;

use App\Repository\EnumEntityRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: EnumEntityRepository::class)]
abstract class EnumEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $color = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct(string $slug, string $name, string $description = null, string $color = "#FFFFFF")
    {
        $this->slug         = $slug;
        $this->name         = $name;
        $this->description  = $description ?? $name;
        $this->color        = $color;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }


    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
