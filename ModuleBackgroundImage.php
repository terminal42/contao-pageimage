<?php

/**
 * pageimage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2009-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-pageimage
 */


class ModuleBackgroundImage extends ModulePageImage
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_background_image';


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

        $strBuffer = parent::generate();

        if ($strBuffer == '') {
            return '';
        } elseif ($this->Template->useCss) {
            $GLOBALS['TL_HEAD'][] = $strBuffer;
        } else {
            $GLOBALS['TL_BODY'][] = $strBuffer;
        }
    }


    protected function compile()
    {
        parent::compile();

        $support = array(
            'ie'        => 9,
            'chrome'    => 3,
            'firefox'   => 4,
            'opera'     => 10,
            'safari'    => 5
        );

        $agent = \Environment::get('agent');

        $this->Template->useCss = (isset($support[$agent->browser]) && $agent->version >= $support[$agent->browser]);
    }
}
