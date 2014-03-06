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
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'ModulePageImage'           => 'system/modules/pageimage/ModulePageImage.php',
    'PageImage'                 => 'system/modules/pageimage/PageImage.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_pageimage'             => 'system/modules/pageimage/templates',
));
