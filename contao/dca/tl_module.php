<?php

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['pageimage'] = '{title_legend},name,headline,type;{config_legend},imgSize,inheritPageImage,levelOffset,randomPageImage,allPageImages;{reference_legend:hide},defineRoot;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['inheritPageImage'] = array
(
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => array('tl_class'=>'w50 m12'),
    'sql'           => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_module']['fields']['allPageImages'] = array
(
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => array('tl_class'=>'w50'),
    'sql'           => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_module']['fields']['randomPageImage'] = array
(
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => array('tl_class'=>'w50 m12'),
    'sql'           => "char(1) NOT NULL default ''",
);
