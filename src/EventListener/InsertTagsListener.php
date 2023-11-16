<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\StringUtil;
use Terminal42\PageimageBundle\PageimageHelper;

#[AsHook('replaceInsertTags')]
class InsertTagsListener
{
    public function __construct(private readonly PageimageHelper $helper)
    {
    }

    public function __invoke(string $tag): string|null
    {
        $tokens = StringUtil::trimsplit('::', $tag);

        switch ($tokens[0]) {
            case 'pageimage':
                $tokens[0] = 'pageimage_path';
                // no break

            case 'pageimage_alt':
            case 'pageimage_title':
            case 'pageimage_href':
                global $objPage;

                $image = $this->helper->getOneByPageAndIndex(
                    $objPage,
                    isset($tokens[1]) ? (int) $tokens[1] : null,
                );

                $key = str_replace('pageimage_', '', (string) $tokens[0]);

                if (null === $image || !isset($image[$key])) {
                    return '';
                }

                return $image[$key];
        }

        return false;
    }
}
