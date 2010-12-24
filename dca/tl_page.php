<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2009-2010
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
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
	'eval'			=> array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'jpg,jpeg,gif,png'),
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

