<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Command;

use App\Core\Domain\Service\WarningGeneratorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:warnings:generate',
    description: 'Generates warnings based on current financial data',
)]
class GenerateWarningsCommand extends Command
{
    /**
     * @param iterable<WarningGeneratorInterface> $warningGenerators
     */
    public function __construct(
        private readonly iterable $warningGenerators
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Warning Generator');

        $totalStats = [
            'added' => 0,
            'maintained' => 0,
            'closed' => 0
        ];

        foreach ($this->warningGenerators as $generator) {
            $io->section('Generating warnings for type: ' . $generator->getType());

            $startTime = microtime(true);
            $stats = $generator->generate();
            $duration = round((microtime(true) - $startTime) * 1000);

            $io->table(
                ['Added', 'Maintained', 'Closed', 'Time (ms)'],
                [[$stats['added'], $stats['maintained'], $stats['closed'], $duration]]
            );

            $totalStats['added'] += $stats['added'];
            $totalStats['maintained'] += $stats['maintained'];
            $totalStats['closed'] += $stats['closed'];
        }

        $io->section('Summary');
        $io->table(
            ['Added', 'Maintained', 'Closed'],
            [[$totalStats['added'], $totalStats['maintained'], $totalStats['closed']]]
        );

        $io->success('Warning generation completed successfully.');

        return Command::SUCCESS;
    }
}
