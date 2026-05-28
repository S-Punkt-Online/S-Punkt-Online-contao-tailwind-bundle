<?php

declare(strict_types=1);

namespace SPunktOnline\ContaoTailwindBundle\Controller\Backend;

use Contao\Backend;
use Contao\BackendTemplate;
use Contao\Input;
use Contao\System;
use Symfony\Component\Process\Process;

class TailwindBackendController extends Backend
{
  public function generate(): string
  {
    $projectDir = \dirname($_SERVER['DOCUMENT_ROOT']);
    $message = '';

    $buildStatus = System::getContainer()
      ->get('SPunktOnline\ContaoTailwindBundle\Service\TailwindBuildStatus');

    if (Input::get('build') === '1') {
      $process = new Process([
        'php83',
        $projectDir . '/vendor/bin/contao-console',
        'tailwind:build',
      ], $projectDir);

      $process->setTimeout(300);
      $process->run();

      $message = $process->isSuccessful()
        ? 'Tailwind CSS wurde erfolgreich neu erstellt.'
        : 'Tailwind Build fehlgeschlagen: ' . $process->getErrorOutput() . $process->getOutput();
    }

    $status = $buildStatus->getStatus($projectDir);

    $template = new BackendTemplate('be_tailwind_css');

    $template->message = $message;
    $template->lastBuild = $status['lastBuild'] ?? 'Noch kein Build';
    $template->buildRequired = ($status['buildRequired'] ?? false) ? 'Ja' : 'Nein';
    $template->lastCron = $status['lastCron'] ?? 'Noch nicht ausgeführt';
    $template->lastDuration = isset($status['lastDuration']) ? $status['lastDuration'] . ' Sekunden' : 'Unbekannt';
    $template->lastOutput = $status['lastOutput'] ?? '';

    return $template->parse();
  }
}