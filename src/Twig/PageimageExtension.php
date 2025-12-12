<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PageimageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pageimage', [PageimageRuntime::class, '__invoke']),
        ];
    }
}
