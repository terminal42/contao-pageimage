<?php

use Contao\Config;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

$pm = PaletteManipulator::create()
    ->addLegend('image_legend', 'meta_legend', PaletteManipulator::POSITION_BEFORE, true)
    ->addField(['pageImage', 'pageImageOverwriteMeta'], 'image_legend', PaletteManipulator::POSITION_APPEND)
;

foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $name => $palette) {
    if ('__selector__' === $name) {
        continue;
    }

    $pm->applyToPalette($name, 'tl_page');
    $GLOBALS['TL_DCA']['tl_page']['fields']['type']['eval']['gallery_types'][] = $name;
}

unset($name, $palette, $pm);

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'pageImageOverwriteMeta';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['pageImageOverwriteMeta'] = 'pageImageUrl,pageImageTitle,pageImageAlt';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['pageImage'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_page']['pageImage'],
    'inputType'     => 'fileTree',
    'exclude'       => true,
    'eval'          => array('fieldType'=>'checkbox', 'orderField'=>'pageImageOrder', 'multiple'=>true, 'files'=>true, 'filesOnly'=>true, 'extensions'=> Config::get('validImageTypes'), 'isGallery'=>true),
    'sql'           => 'blob NULL',
);

// field is used to store the order of the list of images in pageImage
$GLOBALS['TL_DCA']['tl_page']['fields']['pageImageOrder'] = array
(
    'eval'          => array('doNotShow'=>true),
    'sql'           => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pageImageUrl'] = array
(
    'exclude'       => true,
    'search'        => true,
    'inputType'     => 'text',
    'eval'          => array('rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'dcaPicker'=>true, 'addWizardClass'=>false, 'tl_class'=>'w50'),
    'sql'           => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pageImageOverwriteMeta'] = array
(
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => array('submitOnChange'=>true, 'tl_class'=>'w50 clr'),
    'sql'           => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pageImageAlt'] = array
(
    'inputType'     => 'text',
    'exclude'       => true,
    'eval'          => array('maxlength'=>255, 'tl_class'=>'w50'),
    'sql'           => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pageImageTitle'] = array
(
    'inputType'     => 'text',
    'exclude'       => true,
    'eval'          => array('maxlength'=>255, 'tl_class'=>'w50'),
    'sql'           => "varchar(255) NOT NULL default ''",
);
