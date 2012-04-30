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
 * @license    LGPL
 */


/**
 * Palettes
 */
foreach( $GLOBALS['TL_DCA']['tl_page']['palettes'] as $name => $palette )
{
	if ($name == '__selector__')
		continue;

	$GLOBALS['TL_DCA']['tl_page']['palettes'][$name] = str_replace('{meta_legend}', '{image_legend:hide},pageImage,pageImageJumpTo,pageImageAlt,pageImageTitle;{meta_legend}', $palette);
}


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['pageImage'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['pageImage'],
	'inputType'		=> 'fileTree',
	'exclude'		=> true,
	'eval'			=> array('fieldType'=>'checkbox', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'jpg,jpeg,gif,png'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pageImageJumpTo'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['pageImageJumpTo'],
	'inputType'		=> 'pageTree',
	'exclude'		=> true,
	'eval'			=> array('fieldType'=>'radio'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pageImageAlt'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['pageImageAlt'],
	'inputType'		=> 'text',
	'exclude'		=> true,
	'eval'			=> array('maxlength'=>255, 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pageImageTitle'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_page']['pageImageTitle'],
	'inputType'		=> 'text',
	'exclude'		=> true,
	'eval'			=> array('maxlength'=>255, 'tl_class'=>'w50'),
);

