<?php

namespace App\Entity\Accounting;

use App\Entity\EntityInterface;
use App\Entity\Provider;
use App\Entity\Tva;
use App\Repository\Accounting\OrderRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[UniqueEntity('reference')]
class Order implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $reference = null;

    #[ORM\Column(type: 'float')]
    private ?float $ht = null;

    #[ORM\Column(type: 'float')]
    private ?float $ttc = null;

    #[ORM\ManyToOne(targetEntity: Tva::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tva $tva = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provider $provider = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $deliveryDate = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $deliveryPlannedDate = null;

    #[ORM\ManyToOne(targetEntity: PaymentMethod::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentMethod $paymentMethod = null;

    #[ORM\ManyToOne(targetEntity: OrderState::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OrderState $state = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderLine::class, cascade: ['persist', 'remove'])]
    private Collection $orderLines;

    private string $workflowState = 'draft';

    public function __construct()
    {
        $this->reference = '#'.uniqid();
        $this->deliveryPlannedDate = new \DateTime();
        $this->orderLines = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getHt(): ?float
    {
        return $this->ht;
    }

    /**
     * @param float|null $ht
     */
    public function setHt(?float $ht): void
    {
        $this->ht = $ht;
    }

    /**
     * @return float|null
     */
    public function getTtc(): ?float
    {
        return $this->ttc;
    }

    /**
     * @param float|null $ttc
     */
    public function setTtc(?float $ttc): void
    {
        $this->ttc = $ttc;
    }

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getDeliveryDate(): ?DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getDeliveryPlannedDate(): ?DateTimeInterface
    {
        return $this->deliveryPlannedDate;
    }

    public function setDeliveryPlannedDate(DateTimeInterface $deliveryPlannedDate): self
    {
        $this->deliveryPlannedDate = $deliveryPlannedDate;

        return $this;
    }

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getState(): ?OrderState
    {
        return $this->state;
    }

    public function setState(?OrderState $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkflowState(): string
    {
        return $this->state->getSlug() ?? $this->workflowState;
    }

    /**
     * @param string $workflowState
     */
    public function setWorkflowState(string $workflowState): void
    {
        $this->workflowState = $workflowState;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines[] = $orderLine;
            $orderLine->setOrder($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getOrder() === $this) {
                $orderLine->setOrder(null);
            }
        }

        return $this;
    }

    public function calculate(): self
    {
        $ht = 0;
        foreach ($this->getOrderLines() as $invoiceLine) {
            $ht += $invoiceLine->calculateHt();
        }

        $this->setHt($ht);
        $this->setTtc($ht * $this->getTva()->value());

        return $this;
    }

    public function __toString(): string
    {
        return $this->reference;
    }
}
