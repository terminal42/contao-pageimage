<?php

/**
 * pageimage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2009-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-pageimage
 */


/**
 * Frontend modules
 */
$GLOBALS['FE_MOD']['miscellaneous']['pageImage']        = 'ModulePageImage';
$GLOBALS['FE_MOD']['miscellaneous']['backgroundImage']  = 'ModuleBackgroundImage';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('PageImage', 'replaceTags');
