<?php

declare(strict_types=1);

namespace App\Finance\Infrastructure\Fixtures;

use App\Finance\Domain\Entity\Contractor;
use App\Finance\Domain\Entity\Budget;
use App\Finance\Domain\Entity\Invoice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;

class FinanceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $contractor1 = new Contractor('Acme Corp');
        $contractor2 = new Contractor('Beta Ltd');
        $contractor3 = new Contractor('Gamma Inc');
        $manager->persist($contractor1);
        $manager->persist($contractor2);
        $manager->persist($contractor3);

        $budget1 = new Budget('Main Budget', 10000.0);
        $budget2 = new Budget('Negative Budget', -500.0);
        $manager->persist($budget1);
        $manager->persist($budget2);

        $invoice1 = new Invoice('INV-001', $contractor1, 12000.0, (new DateTimeImmutable('-30 days')));
        $invoice2 = new Invoice('INV-002', $contractor1, 4000.0, (new DateTimeImmutable('-10 days')));
        $invoice3 = new Invoice('INV-003', $contractor2, 5000.0, (new DateTimeImmutable('+10 days')));
        $invoice4 = new Invoice('INV-004', $contractor3, 2000.0, (new DateTimeImmutable('-5 days')));
        $invoice2->markAsPaid();
        $manager->persist($invoice1);
        $manager->persist($invoice2);
        $manager->persist($invoice3);
        $manager->persist($invoice4);

        $manager->flush();
    }
}
