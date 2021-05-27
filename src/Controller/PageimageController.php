<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\Controller;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Image;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Picture;
use Contao\StringUtil;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Terminal42\PageimageBundle\PageimageHelper;

/**
 * @FrontendModule(category="miscellaneous")
 */
class PageimageController extends AbstractFrontendModuleController
{
    /**
     * @var PageimageHelper
     */
    private $helper;

    public function __construct(PageimageHelper $helper)
    {
        $this->helper = $helper;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        if ($model->defineRoot) {
            $objPage = PageModel::findByPk($model->rootPage);
        } else {
            global $objPage;
        }

        if (null === $objPage) {
            return new Response();
        }

        $image = $this->helper->getOneByPageAndIndex(
            $objPage,
            $model->randomPageImage ? null : (int) $model->levelOffset,
            (bool) $model->inheritPageImage
        );

        if (null === $image) {
            return new Response();
        }

        $size = StringUtil::deserialize($model->imgSize);
        $image['src'] = Image::get($image['path'], $size[0], $size[1], $size[2]);

        $template->setData(array_merge($template->getData(), $image));

        $picture = Picture::create($image['path'], $size)->getTemplateData();
        $picture['alt'] = StringUtil::specialchars($image['alt']);
        $template->picture = $picture;

        if (false !== ($imgSize = @getimagesize(TL_ROOT.'/'.rawurldecode($image['src'])))) {
            $template->size = ' '.$imgSize[3];
        }

        // Lazy-load the media queries
        $template->mediaQueries = function () use ($picture) {
            return $this->compileMediaQueries($picture);
        };

        return $template->getResponse();
    }

    private function compileMediaQueries(array $picture)
    {
        $mediaQueries = [];
        $sources = [$picture['img']];

        if (\is_array($picture['sources'])) {
            $sources = array_merge($sources, $picture['sources']);
        }

        foreach ($sources as $value) {
            foreach (StringUtil::trimsplit(',', $value['srcset']) as $srcset) {
                [$src, $density] = StringUtil::trimsplit(' ', $srcset);

                if (null === $density) {
                    continue;
                }

                $density = rtrim($density, 'x');

                if (1 !== (int) $density || !empty($value['media'])) {
                    $mediaQueries[] = [
                        'mq' => sprintf(
                            $density > 1 ? 'screen and %1$s%2$s, screen and %1$s%3$s' : 'screen and %1$s',
                            $value['media'] ?: '',
                            $density > 1 ? " and (-webkit-min-device-pixel-ratio: $density)" : '',
                            $density > 1 ? " and (min-resolution: {$density}dppx)" : ''
                        ),
                        'src' => $src,
                    ];
                }
            }
        }

        return $mediaQueries;
    }
}
