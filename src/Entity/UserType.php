<?php

namespace App\Entity;

use App\Repository\UserTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserTypeRepository::class)]
class UserType extends EnumEntity
{
    #[ORM\OneToMany(mappedBy: 'profession', targetEntity: User::class)]
    private Collection $users;

    public function __construct(string $slug, string $name, string $description = null, string $color = "#FFFFFF")
    {
        parent::__construct($slug, $name, $description, $color);
        $this->users = new ArrayCollection();
    }

    /**
     * @return Collection<User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }
}
