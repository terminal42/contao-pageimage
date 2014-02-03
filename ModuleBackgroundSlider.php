<?php

/**
 * pageimage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2009-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-pageimage
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
        $this->Template->slideshow         = (count($arrImages) > 1 ? $this->slider_autostart : 0);
        $this->Template->transition        = $this->slider_transition;
        $this->Template->slide_interval    = $this->slider_interval;
        $this->Template->speed             = $this->slider_animationDuration;
        $this->Template->randomimage       = (int) $this->slider_randomOrder;
    }
}
