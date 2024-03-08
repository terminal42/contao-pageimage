<?php

declare(strict_types=1);

namespace Terminal42\PageimageBundle\Controller;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Terminal42\PageimageBundle\PageimageHelper;

#[AsFrontendModule(category: 'miscellaneous', template: 'mod_pageimage')]
class PageimageController extends AbstractFrontendModuleController
{
    public function __construct(
        private readonly PageimageHelper $helper,
        private readonly Studio $studio,
    ) {
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $images = $this->getImages($model);

        if (empty($images)) {
            return new Response();
        }

        $templateData = [];
        $figure = $this->studio->createFigureBuilder()->setSize($model->imgSize);

        foreach ($images as $image) {
            $templateData[] = $figure->from($image['path'])->build()->getLegacyTemplateData();
        }

        $template->setData(array_merge($template->getData(), $templateData[0]));
        $template->allImages = $templateData;

        // Lazy-load the media queries
        $template->mediaQueries = fn () => $this->compileMediaQueries($templateData[0]['picture']);

        return $template->getResponse();
    }

    private function getImages(ModuleModel $model): array
    {
        if ($model->defineRoot) {
            $pageModel = PageModel::findByPk($model->rootPage);
        } else {
            $pageModel = $this->getPageModel();
        }

        if (null === $pageModel) {
            return [];
        }

        $images = $this->helper->findForPage($pageModel, (bool) $model->inheritPageImage);

        if (null === $images) {
            return [];
        }

        if ($model->allPageImages) {
            return $images;
        }

        if ($model->randomPageImage) {
            $index = random_int(0, \count($images) - 1);
        } else {
            $index = $model->levelOffset;
        }

        if (!isset($images[$index])) {
            return [];
        }

        return [$images[$index]];
    }

    private function compileMediaQueries(array $picture): array
    {
        $mediaQueries = [];
        $sources = [$picture['img']];

        if (\is_array($picture['sources'])) {
            $sources = array_merge($sources, $picture['sources']);
        }

        foreach ($sources as $value) {
            foreach (StringUtil::trimsplit(',', $value['srcset']) as $srcset) {
                [$src, $density] = StringUtil::trimsplit(' ', $srcset) + [null, null];

                if (null === $density || !str_ends_with((string) $density, 'x')) {
                    continue;
                }

                $density = rtrim((string) $density, 'x');

                if (1 !== (int) $density || !empty($value['media'])) {
                    $mediaQueries[] = [
                        'mq' => sprintf(
                            $density > 1 ? 'screen %1$s%2$s, screen and %1$s%3$s' : 'screen and %1$s',
                            $value['media'] ? " and {$value['media']}" : '',
                            $density > 1 ? " and (-webkit-min-device-pixel-ratio: $density)" : '',
                            $density > 1 ? " and (min-resolution: {$density}dppx)" : '',
                        ),
                        'src' => $src,
                    ];
                }
            }
        }

        return $mediaQueries;
    }
}
