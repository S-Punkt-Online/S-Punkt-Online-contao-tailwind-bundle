<?php

declare(strict_types=1);

namespace SPunktOnline\ContaoTailwindBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(
  name: 'tailwind:watch',
  description: 'Watches and rebuilds Tailwind CSS for Contao.'
)]
class TailwindWatchCommand extends Command
{
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    $projectDir = getcwd();

    $inputFile = $projectDir . '/packages/contao-tailwind-bundle/assets/input.css';
    $outputFile = $projectDir . '/public/assets/tailwind/tailwind.css';

    if (!is_dir(dirname($outputFile))) {
      mkdir(dirname($outputFile), 0775, true);
    }

    $io->info('Tailwind Watch gestartet. Zum Beenden: Ctrl + C');

    $process = new Process([
      'npx',
      '@tailwindcss/cli',
      '-i',
      $inputFile,
      '-o',
      $outputFile,
      '--watch',
    ]);

    $process->setTimeout(null);
    $process->run(fn ($type, $buffer) => $output->write($buffer));

    return $process->isSuccessful()
      ? Command::SUCCESS
      : Command::FAILURE;
  }
}