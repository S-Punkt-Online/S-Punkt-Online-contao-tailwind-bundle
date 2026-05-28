<?php

declare(strict_types=1);

namespace SPunktOnline\ContaoTailwindBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use SPunktOnline\ContaoTailwindBundle\Service\TailwindContentSourceGenerator;
use SPunktOnline\ContaoTailwindBundle\Service\TailwindBuildStatus;

#[AsCommand(
  name: 'tailwind:build',
  description: 'Builds the Tailwind CSS file for Contao.'
)]
class TailwindBuildCommand extends Command
{
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    $projectDir = getcwd();


    $this->contentSourceGenerator->generate($projectDir);

    $inputFile = $projectDir . '/packages/contao-tailwind-bundle/assets/input.css';

    $inputFile = $projectDir . '/packages/contao-tailwind-bundle/assets/input.css';
    $outputFile = $projectDir . '/public/assets/tailwind/tailwind.css';

    if (!is_dir(dirname($outputFile))) {
      mkdir(dirname($outputFile), 0775, true);
    }

    $process = new Process([
      'npx',
      '@tailwindcss/cli',
      '-i',
      $inputFile,
      '-o',
      $outputFile,
      '--minify',
    ]);

    $process->setTimeout(300);
    $startTime = microtime(true);
    $process->run(fn ($type, $buffer) => $output->write($buffer));
    $duration = round(microtime(true) - $startTime, 2);

    if (!$process->isSuccessful()) {
      $io->error('Tailwind Build fehlgeschlagen.');
      return Command::FAILURE;
    }

    $io->success('Tailwind CSS erfolgreich kompiliert.');
    $this->buildStatus->markBuildDone(
      $projectDir,
      $duration,
      $process->getOutput() . $process->getErrorOutput()
    );

    return Command::SUCCESS;
  }

  public function __construct(
    private readonly TailwindContentSourceGenerator $contentSourceGenerator,
    private readonly TailwindBuildStatus $buildStatus,
  ) {
    parent::__construct();
  }
}