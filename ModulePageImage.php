<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *
 * PHP version 5
 * @copyright  terminal42 gmbh 2009-2013
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Kamil Kuźmiński <kamil.kuzminski@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
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

        $this->import('PageImage');

        $strBuffer = parent::generate();

        if ($this->Template->src == '')
            return '';

        return $strBuffer;
    }


    protected function compile()
    {
        $arrImage = $this->PageImage->getPageImage(false, $this->levelOffset, null, $this->inheritPageImage);

        if ($arrImage === false)
        {
            return;
        }

        $arrSize = deserialize($this->imgSize);
        $arrImage['src'] = $this->getImage($arrImage['path'], $arrSize[0], $arrSize[1], $arrSize[2]);

        $this->Template->setData($arrImage);

        if (($imgSize = @getimagesize(TL_ROOT . '/' . $arrImage['path'])) !== false)
        {
            $this->Template->size = ' ' . $imgSize[3];
        }
    }
}