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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['pageImage'] = '{title_legend},name,headline,type;{config_legend},imgSize,inheritPageImage,levelOffset,randomPageImage;{reference_legend:hide},defineRoot;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['backgroundImage'] = '{title_legend},name,type;{config_legend},imgSize,inheritPageImage,levelOffset,randomPageImage;{reference_legend:hide},defineRoot;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['inheritPageImage'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_module']['inheritPageImage'],
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => array('tl_class'=>'w50 m12'),
    'sql'           => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_module']['fields']['randomPageImage'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_module']['randomPageImage'],
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => array('tl_class'=>'w50 m12'),
    'sql'           => "char(1) NOT NULL default ''",
);
