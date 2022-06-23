<?php

namespace App\Entity\Accounting;

use App\Entity\EnumEntity;
use App\Repository\Accounting\OrderStateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderStateRepository::class)]
class OrderState extends EnumEntity
{
    #[ORM\OneToMany(mappedBy: 'state', targetEntity: Order::class)]
    private Collection $orders;

    public function __construct(string $slug, string $name, string $description = null, string $color = "#FFFFFF")
    {
        parent::__construct($slug, $name, $description, $color);
        $this->orders = new ArrayCollection();
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setState($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getState() === $this) {
                $order->setState(null);
            }
        }

        return $this;
    }
}
