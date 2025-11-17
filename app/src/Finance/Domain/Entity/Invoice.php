<?php

declare(strict_types=1);

namespace App\Finance\Domain\Entity;

use App\Finance\Domain\Repository\InvoiceRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepositoryInterface::class)]
#[ORM\Table(name: 'finance_invoices')]
#[ORM\HasLifecycleCallbacks]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private string $number;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contractor $contractor = null;

    #[ORM\Column]
    private float $amount;

    #[ORM\Column]
    private bool $isPaid = false;

    #[ORM\Column]
    private DateTimeImmutable $dueDate;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(string $number, Contractor $contractor, float $amount, DateTimeImmutable $dueDate)
    {
        $this->number = $number;
        $this->contractor = $contractor;
        $this->amount = $amount;
        $this->dueDate = $dueDate;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getContractor(): ?Contractor
    {
        return $this->contractor;
    }

    public function setContractor(?Contractor $contractor): void
    {
        $this->contractor = $contractor;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function markAsPaid(): void
    {
        $this->isPaid = true;
    }

    public function markAsUnpaid(): void
    {
        $this->isPaid = false;
    }

    public function getDueDate(): DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(DateTimeImmutable $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
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

    public function delete(): self
    {
        $this->deletedAt = new DateTimeImmutable();
        return $this;
    }

    public function restore(): self
    {
        $this->deletedAt = null;
        return $this;
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

    public function isOverdue(): bool
    {
        if ($this->isPaid) {
            return false;
        }

        $now = new DateTimeImmutable();
        return $this->dueDate < $now;
    }
}
