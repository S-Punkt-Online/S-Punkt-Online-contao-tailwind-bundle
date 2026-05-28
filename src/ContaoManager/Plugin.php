<?php

declare(strict_types=1);

namespace SPunktOnline\ContaoTailwindBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use SPunktOnline\ContaoTailwindBundle\ContaoTailwindBundle;

class Plugin implements BundlePluginInterface
{
  public function getBundles(ParserInterface $parser): array
  {
    return [
      BundleConfig::create(ContaoTailwindBundle::class)
        ->setLoadAfter([
          \Contao\CoreBundle\ContaoCoreBundle::class,
        ]),
    ];
  }
}