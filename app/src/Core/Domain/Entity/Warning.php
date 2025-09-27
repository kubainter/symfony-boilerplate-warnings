<?php

declare(strict_types=1);

namespace App\Core\Domain\Entity;

use App\Core\Domain\Repository\WarningRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarningRepositoryInterface::class)]
#[ORM\Table(name: 'core_warnings')]
#[ORM\HasLifecycleCallbacks]
class Warning
{
    public const CATEGORY_CONTRACTOR_OVERDUE_LIMIT = 'contractor overdue limit exceeded';
    public const CATEGORY_INVOICE_OVERDUE = 'invoice overdue';
    public const CATEGORY_BUDGET_NEGATIVE = 'budget below zero';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $objectType;

    #[ORM\Column]
    private int $objectId;

    #[ORM\Column(length: 100)]
    private string $category;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(string $objectType, int $objectId, string $category)
    {
        $this->objectType = $objectType;
        $this->objectId = $objectId;
        $this->category = $category;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function createForContractorOverdueLimit(int $contractorId): self
    {
        return new self(
            'contractor',
            $contractorId,
            self::CATEGORY_CONTRACTOR_OVERDUE_LIMIT
        );
    }

    public static function createForOverdueInvoice(int $invoiceId): self
    {
        return new self(
            'invoice',
            $invoiceId,
            self::CATEGORY_INVOICE_OVERDUE
        );
    }

    public static function createForNegativeBudget(int $budgetId): self
    {
        return new self(
            'budget',
            $budgetId,
            self::CATEGORY_BUDGET_NEGATIVE
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function getObjectId(): int
    {
        return $this->objectId;
    }

    public function getCategory(): string
    {
        return $this->category;
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
}
