<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle;

use Contao\File;
use Contao\FilesModel;
use Contao\Frontend;
use Contao\PageModel;
use Contao\StringUtil;

class PageimageHelper
{
    /**
     * @var array
     */
    protected static $imagesCache = [];

    public function getOneByPageAndIndex(PageModel $page, ?int $index = 0, bool $inherit = true): ?array
    {
        $images = $this->findForPage($page, $inherit);

        if (null === $images) {
            return null;
        }

        // Random image
        if (null === $index) {
            $index = random_int(0, \count($images) - 1);
        }

        if (!isset($images[$index])) {
            return null;
        }

        return $images[$index];
    }

    public function findForPage(PageModel $page, bool $inherit = true): ?array
    {
        if (!isset(static::$imagesCache[$page->id])) {
            static::$imagesCache[$page->id] = false;

            $images = $this->parsePage($page);

            if (!empty($images)) {
                static::$imagesCache[$page->id] = [
                    'images' => $images,
                    'inherited' => false,
                ];
            } else {
                $page->loadDetails();
                $parentPages = PageModel::findMultipleByIds(array_reverse($page->trail));

                if (null !== $parentPages) {
                    foreach ($parentPages as $parentPage) {
                        $images = $this->parsePage($parentPage);

                        if (!empty($images)) {
                            static::$imagesCache[$page->id] = [
                                'images' => $images,
                                'inherited' => true,
                            ];

                            break;
                        }
                    }
                }
            }
        }

        if (false === static::$imagesCache[$page->id] || (!$inherit && static::$imagesCache[$page->id]['inherited'])) {
            return null;
        }

        return static::$imagesCache[$page->id]['images'];
    }

    private function parsePage(PageModel $page): array
    {
        if (empty($page->pageImage)) {
            return [];
        }

        $images = [];
        $files = FilesModel::findMultipleByUuids(StringUtil::deserialize($page->pageImage, true));

        if (null !== $files) {
            foreach ($files as $file) {
                $objFile = new File($file->path);

                if (!$objFile->isImage) {
                    continue;
                }

                $image = $file->row();
                $meta = Frontend::getMetaData($file->meta, $page->language);

                // Use the file name as title if none is given
                if (empty($meta['title'])) {
                    $meta['title'] = StringUtil::specialchars($objFile->basename);
                }

                $image['alt'] = $meta['alt'] ?? '';
                $image['imageUrl'] = $meta['link'] ?? '';
                $image['caption'] = $meta['caption'] ?? '';
                $image['title'] = $meta['title'] ?? '';
                $image['imageTitle'] = $image['title'];
                $image['meta'] = $meta;
                $image['hasLink'] = false;
                $image['href'] = '';

                if ($page->pageImageOverwriteMeta) {
                    $image['alt'] = $page->pageImageAlt;
                    $image['title'] = $page->pageImageTitle;
                    $image['imageTitle'] = $page->pageImageTitle;
                    $image['imageUrl'] = $page->pageImageUrl;

                    if (!empty($page->pageImageUrl)) {
                        $image['hasLink'] = true;
                        $image['href'] = $page->pageImageUrl;
                    }
                }

                $images[] = $image;
            }

            $images = $this->sortImages($images, $page);
        }

        return $images;
    }

    private function sortImages(array $images, PageModel $pageModel): array
    {
        $order = StringUtil::deserialize($pageModel->pageImageOrder);

        if (empty($order) || !\is_array($order)) {
            return $images;
        }

        // Remove all values
        $order = array_map(static function (): void {}, array_flip($order));

        // Move the matching elements to their position in $order
        foreach ($images as $k => $v) {
            if (\array_key_exists($v['uuid'], $order)) {
                $order[$v['uuid']] = $v;
                unset($images[$k]);
            }
        }

        // Append the left-over images at the end
        if (!empty($images)) {
            $order = array_merge($order, array_values($images));
        }

        // Remove empty (unreplaced) entries
        return array_values(array_filter($order));
    }
}
