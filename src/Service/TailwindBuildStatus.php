<?php

declare(strict_types=1);

namespace SPunktOnline\ContaoTailwindBundle\Service;

class TailwindBuildStatus
{
  public function getStatus(string $projectDir): array
  {
    $file = $this->getStatusFile($projectDir);

    if (!is_file($file)) {
      return [
        'buildRequired' => false,
        'lastBuild' => null,
      ];
    }

    $data = json_decode((string) file_get_contents($file), true);

    return is_array($data) ? $data : [
      'buildRequired' => false,
      'lastBuild' => null,
    ];
  }

  public function markBuildDone(string $projectDir, ?float $duration = null, ?string $output = null): void
  {
    $status = $this->getStatus($projectDir);

    $this->writeStatus($projectDir, [
      'buildRequired' => false,
      'lastBuild' => date('Y-m-d H:i:s'),
      'lastCron' => $status['lastCron'] ?? null,
      'lastDuration' => $duration,
      'lastOutput' => $output,
    ]);
  }

  public function markCronRun(string $projectDir): void
  {
    $status = $this->getStatus($projectDir);

    $status['lastCron'] = date('Y-m-d H:i:s');

    $this->writeStatus($projectDir, $status);
  }

  public function markBuildRequired(string $projectDir): void
  {
    $status = $this->getStatus($projectDir);

    $this->writeStatus($projectDir, [
      'buildRequired' => true,
      'lastBuild' => $status['lastBuild'] ?? null,
    ]);
  }

  private function writeStatus(string $projectDir, array $data): void
  {
    $dir = $projectDir . '/var/tailwind';

    if (!is_dir($dir)) {
      mkdir($dir, 0775, true);
    }

    file_put_contents(
      $this->getStatusFile($projectDir),
      json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
  }

  private function getStatusFile(string $projectDir): string
  {
    return $projectDir . '/var/tailwind/status.json';
  }
}