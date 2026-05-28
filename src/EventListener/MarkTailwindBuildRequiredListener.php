<?php

declare(strict_types=1);

namespace SPunktOnline\ContaoTailwindBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\DataContainer;
use Contao\System;
use SPunktOnline\ContaoTailwindBundle\Service\TailwindBuildStatus;

#[AsHook('loadDataContainer')]
class MarkTailwindBuildRequiredListener
{
  private const TABLES = [
    'tl_content',
    'tl_module',
    'tl_article',
    'tl_layout',
    'tl_page',
    'tl_form',
    'tl_form_field',
    'tl_theme',
  ];

  public function __invoke(string $table): void
  {
    if (!in_array($table, self::TABLES, true)) {
      return;
    }

    $GLOBALS['TL_DCA'][$table]['config']['onsubmit_callback'][] = [
      $this,
      'markBuildRequired',
    ];
  }

  public function markBuildRequired(DataContainer $dc): void
  {
    $projectDir = \dirname($_SERVER['DOCUMENT_ROOT']);

    System::getContainer()
      ->get(TailwindBuildStatus::class)
      ->markBuildRequired($projectDir);
  }
}