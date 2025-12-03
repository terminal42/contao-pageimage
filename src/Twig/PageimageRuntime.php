<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\Twig;

use Contao\PageModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Terminal42\PageimageBundle\PageimageHelper;
use Twig\Extension\RuntimeExtensionInterface;

class PageimageRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly PageimageHelper $helper,
    ) {
    }

    public function __invoke(int|null $index = 0, int|null $page = null): array|null
    {
        if (null === $page) {
            $pageModel = $this->getCurrentPage();
        } else {
            $pageModel = PageModel::findById($page);
        }

        if (null === $pageModel) {
            return null;
        }

        return $this->helper->getOneByPageAndIndex($pageModel, $index);
    }

    public function getCurrentPage(): PageModel|null
    {
        $pageModel = $this->requestStack->getCurrentRequest()?->attributes->get('pageModel');

        if ($pageModel instanceof PageModel) {
            return $pageModel;
        }

        return null;
    }
}
