<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2009
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Palettes
 */
foreach( $GLOBALS['TL_DCA']['tl_page']['palettes'] as $strName => $strPalette )
{
	if ($strName == '__selector__')
		continue;
		
	$GLOBALS['TL_DCA']['tl_page']['palettes'][$strName] = str_replace('{meta_legend}', '{image_legend},pageImage,pageImageJumpTo;{meta_legend}', $strPalette);
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

