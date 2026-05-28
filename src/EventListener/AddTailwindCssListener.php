<?php

declare(strict_types=1);

namespace SPunktOnline\ContaoTailwindBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('modifyFrontendPage')]
class AddTailwindCssListener
{
  public function __invoke(string $buffer, string $template): string
  {
    $cssPath = 'assets/tailwind/tailwind.css';
    $absolutePath = \dirname(__DIR__, 4) . '/public/' . $cssPath;

    if (!is_file($absolutePath)) {
      return $buffer;
    }

    $tag = '<link rel="stylesheet" href="/' . $cssPath . '?v=' . filemtime($absolutePath) . '">';

    if (str_contains($buffer, $tag)) {
      return $buffer;
    }

    return str_replace('</head>', '    ' . $tag . "\n</head>", $buffer);
  }
}