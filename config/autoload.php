<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Pageimage
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'ModulePageImage' => 'system/modules/pageimage/ModulePageImage.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_pageimage' => 'system/modules/pageimage/templates',
));
