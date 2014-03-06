<?php

/**
 * pageimage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2009-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-pageimage
 */


class ModulePageImage extends Module
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_pageimage';


    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### PAGE IMAGE ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'typolight/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $strBuffer = parent::generate();

        if ($this->Template->src == '') {
            return '';
        }

        return $strBuffer;
    }


    protected function compile()
    {
        if ($this->defineRoot) {
            $objPage = \PageModel::findByPk($this->rootPage);
        } else {
            global $objPage;
        }

        if (null === $objPage) {
            return;
        }

        $arrImage = PageImage::getOne($objPage, (int) $this->levelOffset, (bool) $this->inheritPageImage);

        if (null === $arrImage) {
            return;
        }

        $arrSize = deserialize($this->imgSize);
        $arrImage['src'] = $this->getImage($arrImage['path'], $arrSize[0], $arrSize[1], $arrSize[2]);

        $this->Template->setData($arrImage);

        if (($imgSize = @getimagesize(TL_ROOT . '/' . $arrImage['src'])) !== false) {
            $this->Template->size = ' ' . $imgSize[3];
        }
    }
}
