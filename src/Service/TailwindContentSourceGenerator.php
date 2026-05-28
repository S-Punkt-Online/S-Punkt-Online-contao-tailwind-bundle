<?php

declare(strict_types=1);

namespace SPunktOnline\ContaoTailwindBundle\Service;

use Contao\StringUtil;
use Doctrine\DBAL\Connection;

class TailwindContentSourceGenerator
{
  public function __construct(
    private readonly Connection $connection,
  ) {
  }

  public function generate(string $projectDir): string
  {
    $targetDir = $projectDir . '/var/tailwind';
    $targetFile = $targetDir . '/content-sources.txt';

    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0775, true);
    }

    $classes = [];

    $tables = [
      'tl_content',
      'tl_module',
      'tl_article',
      'tl_layout',
      'tl_page',
      'tl_form',
      'tl_form_field',
      'tl_theme',
    ];

    foreach ($tables as $table) {
      if (!$this->connection->createSchemaManager()->tablesExist([$table])) {
        continue;
      }

      $rows = $this->connection->fetchAllAssociative('SELECT * FROM ' . $table);

      foreach ($rows as $row) {
        foreach ($row as $value) {
          $decodedValue = $this->decodeValue($value);
          $this->collectClasses($decodedValue, $classes);
        }
      }
    }

    $classes = array_values(array_unique($classes));
    sort($classes);

    file_put_contents($targetFile, implode(PHP_EOL, $classes));

    return $targetFile;
  }

  private function decodeValue(mixed $value): mixed
  {
    if (!is_string($value) || $value === '') {
      return $value;
    }

    return StringUtil::deserialize($value, true);
  }

  private function collectClasses(mixed $value, array &$classes): void
  {
    if (is_array($value)) {
      foreach ($value as $item) {
        $this->collectClasses($item, $classes);
      }

      return;
    }

    if (!is_string($value) || $value === '') {
      return;
    }

    preg_match_all('/[A-Za-z0-9_:\-\/\[\]\.]+/', $value, $matches);

    foreach ($matches[0] as $match) {
      if ($this->looksLikeTailwindClass($match)) {
        $classes[] = $match;
      }
    }
  }

  private function looksLikeTailwindClass(string $value): bool
  {
    if (strlen($value) < 3) {
      return false;
    }

    if (str_contains($value, 'http')) {
      return false;
    }

    if (str_contains($value, '.')) {
      return false;
    }

    if (str_contains($value, ':') && !preg_match('/^(sm|md|lg|xl|2xl|hover|focus|active|disabled|group-hover|dark):/', $value)) {
      return false;
    }

    return (bool) preg_match(
      '/^(bg|text|p|m|mt|mb|ml|mr|mx|my|pt|pb|pl|pr|px|py|flex|grid|block|hidden|inline|rounded|shadow|border|w|h|min|max|gap|space|items|justify|content|self|object|overflow|relative|absolute|fixed|sticky|top|right|bottom|left|z|opacity|transition|duration|ease|scale|rotate|translate|font|leading|tracking|container|columns|col|row|order|basis|grow|shrink)-/',
      $value
    );
  }
}