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


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'ModulePageImage'           => 'system/modules/pageimage/ModulePageImage.php',
    'ModuleBackgroundSlider'    => 'system/modules/pageimage/ModuleBackgroundSlider.php',
    'PageImage'                 => 'system/modules/pageimage/PageImage.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_pageimage'             => 'system/modules/pageimage/templates',
    'mod_background_slider'     => 'system/modules/pageimage/templates',
));
