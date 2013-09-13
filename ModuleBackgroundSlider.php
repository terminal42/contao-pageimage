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
 * @author     Jan Reuteler <jan.reuteler@terminal42.ch>
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class ModuleBackgroundSlider extends Module
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_background_slider';


    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### BACKGROUND IMAGE ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'typolight/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }


    protected function compile()
    {
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/pageimage/assets/superbgimage.min.js';

        $arrImages = PageImage::getInstance()->getMultiple($this->levelOffset, ($this->showLevel ?: null), $this->inheritPageImage);

        $this->Template->images = $arrImages;
        $this->Template->settings = array(
            'slideshow'         => (count($arrImages) > 1 ? $this->slider_autostart : 0),
            'transition'        => $this->slider_transition,
            'slide_interval'    => $this->slider_interval,
            'speed'             => $this->slider_animationDuration,
            'randomimage'       => (int)$this->slider_randomOrder,
        );
    }
}
