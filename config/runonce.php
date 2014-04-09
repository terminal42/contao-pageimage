<?php

/**
 * pageimage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2009-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-pageimage
 */


class ImagePageUpgrade
{

    public function run()
    {
        if (version_compare(VERSION, '3.2', '>=') && \Database::getInstance()->fieldExists('pageImage', 'tl_page'))
        {
            \Database\Updater::convertMultiField('tl_page', 'pageImage');
        }
    }
}

$objUpgrade = new ImagePageUpgrade();
$objUpgrade->run();
