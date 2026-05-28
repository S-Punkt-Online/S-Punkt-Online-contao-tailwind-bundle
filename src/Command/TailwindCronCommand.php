<?php

declare(strict_types=1);

namespace SPunktOnline\ContaoTailwindBundle\Command;

use SPunktOnline\ContaoTailwindBundle\Service\TailwindBuildStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(
  name: 'tailwind:cron',
  description: 'Builds Tailwind CSS only if a build is required.'
)]
class TailwindCronCommand extends Command
{
  public function __construct(
    private readonly TailwindBuildStatus $buildStatus,
  ) {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);
    $projectDir = getcwd();

    $status = $this->buildStatus->getStatus($projectDir);
    $this->buildStatus->markCronRun($projectDir);

    if (($status['buildRequired'] ?? false) !== true) {
      $io->success('Kein Tailwind Build erforderlich.');

      return Command::SUCCESS;
    }

    $process = new Process([
      'php',
      $projectDir . '/vendor/bin/contao-console',
      'tailwind:build',
    ], $projectDir);

    $process->setTimeout(300);
    $process->run(fn ($type, $buffer) => $output->write($buffer));

    if (!$process->isSuccessful()) {
      $io->error('Tailwind Cron Build fehlgeschlagen.');

      return Command::FAILURE;
    }

    $io->success('Tailwind Cron Build erfolgreich ausgeführt.');

    return Command::SUCCESS;
  }
}