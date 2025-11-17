<?php

declare(strict_types=1);

namespace App\Finance\Domain\Entity;

use App\Finance\Domain\Repository\ContractorRepositoryInterface;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractorRepositoryInterface::class)]
#[ORM\Table(name: 'finance_contractors')]
#[ORM\HasLifecycleCallbacks]
class Contractor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    #[ORM\OneToMany(mappedBy: 'contractor', targetEntity: Invoice::class)]
    private Collection $invoices;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function delete(): void
    {
        $this->deletedAt = new DateTimeImmutable();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setContractor($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            if ($invoice->getContractor() === $this) {
                $invoice->setContractor(null);
            }
        }

        return $this;
    }

    public function getTotalOverdueAmount(): float
    {
        $total = 0.0;
        $now = new \DateTimeImmutable();

        foreach ($this->invoices as $invoice) {
            if (!$invoice->isPaid() && $invoice->getDueDate() < $now) {
                $total += $invoice->getAmount();
            }
        }

        return $total;
    }

    public function hasExceededOverdueLimit(float $limit = 15000.0): bool
    {
        return $this->getTotalOverdueAmount() > $limit;
    }
}
