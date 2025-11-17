<?php

declare(strict_types=1);

namespace App\Core\Application\Service;

use App\Core\Domain\Entity\Warning;
use App\Core\Domain\Repository\WarningRepositoryInterface;
use App\Core\Domain\Service\WarningGeneratorInterface;
use App\Finance\Domain\Repository\ContractorRepositoryInterface;

class ContractorWarningGenerator implements WarningGeneratorInterface
{
    private const LIMIT = 15000.0;
    private const TYPE = 'contractor';

    public function __construct(
        private readonly ContractorRepositoryInterface $contractorRepository,
        private readonly WarningRepositoryInterface $warningRepository
    ) {
    }

    /**
     * @return array{added: int, maintained: int, closed: int}
     */
    public function generate(): array
    {
        $stats = [
            'added' => 0,
            'maintained' => 0,
            'closed' => 0
        ];

        $contractorsExceedingLimit = $this->contractorRepository->findWithOverdueInvoicesExceedingLimit(self::LIMIT);

        foreach ($contractorsExceedingLimit as $contractor) {
            $existingWarning = $this->warningRepository->findActiveWarningForObject(
                self::TYPE,
                $contractor->getId(),
                Warning::CATEGORY_CONTRACTOR_OVERDUE_LIMIT
            );

            if ($existingWarning === null) {
                $warning = Warning::createForContractorOverdueLimit($contractor->getId());
                $this->warningRepository->save($warning);
                $stats['added']++;
            } else {
                $stats['maintained']++;
            }
        }

        $activeWarnings = $this->warningRepository->findActiveByCategory(Warning::CATEGORY_CONTRACTOR_OVERDUE_LIMIT);

        foreach ($activeWarnings as $warning) {
            $contractorId = $warning->getObjectId();
            $stillExceeding = false;

            foreach ($contractorsExceedingLimit as $contractor) {
                if ($contractor->getId() === $contractorId) {
                    $stillExceeding = true;
                    break;
                }
            }

            if (!$stillExceeding) {
                $warning->delete();
                $this->warningRepository->save($warning);
                $stats['closed']++;
            }
        }

        return $stats;
    }

    public function getType(): string
    {
        return self::TYPE;
    }
}
