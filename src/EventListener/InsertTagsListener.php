<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\StringUtil;
use Terminal42\PageimageBundle\PageimageHelper;

/**
 * @Hook("replaceInsertTags")
 */
class InsertTagsListener
{
    /**
     * @var PageimageHelper
     */
    private $helper;

    public function __construct(PageimageHelper $helper)
    {
        $this->helper = $helper;
    }

    public function __invoke(string $tag)
    {
        $tokens = StringUtil::trimsplit('::', $tag);

        switch ($tokens[0]) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'pageimage':
                $tokens[0] = 'pageimage_path';
                // no break

            case 'pageimage_alt':
            case 'pageimage_title':
            case 'pageimage_href':

                global $objPage;

                $image = $this->helper->getOneByPageAndIndex(
                    $objPage,
                    isset($tokens[1]) ? (int) $tokens[1] : null
                );

                $key = str_replace('pageimage_', '', $tokens[0]);

                if (null === $image || !isset($image[$key])) {
                    return '';
                }

                return $image[$key];
        }

        return false;
    }
}
