<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Terminal42\PageimageBundle\Terminal42PageimageBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            (new BundleConfig(Terminal42PageimageBundle::class))->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
