<?php

/**
 * pageimage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2009-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-pageimage
 */


class PageImage extends Frontend
{

    /**
     * Images
     * @var array
     */
    protected static $arrImages;

    /**
     * Image has been inherited
     * @var bool
     */
    protected static $blnInherited;


    /**
     * Prevent direct instantiation (Singleton)
     */
    protected static function initialize()
    {
        if (null === static::$arrImages) {
            global $objPage;

            // Current page has an image
            if ($objPage->pageImage != '') {
                static::$arrImages = static::parsePage($objPage, $intIndex, $intTotal);
                static::$blnInherited = false;
            }

            // Walk the trail
            else {
                static::$arrImages = array();
                static::$blnInherited = true;

                $objTrail = \PageModel::findMultipleByIds($objPage->trail, array('order'=>Database::getInstance()->findInSet('id', array_reverse($objPage->trail))));

                if (null !== $objTrail) {
                    while ($objTrail->next()) {
                        if ($objTrail->pageImage != '') {
                            static::$arrImages = static::parsePage($objTrail, $intIndex, $intTotal);
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Replace "pageimage" inserttag
     * @param   string
     * @return  mixed
     */
    public static function replaceTags($strTag)
    {
        static::initialize();

        $arrTag = trimsplit('::', $strTag);

        switch($arrTag[0])
        {
            case 'pageimage':
                $arrTag[0] = 'pageimage_src';
                // Do NOT add a break;

            case 'pageimage_alt':
            case 'pageimage_title':
            case 'pageimage_href':
                if (!isset(static::$arrImages[$arrTag[1]])) {
                    return '';
                }

                return static::$arrImages[$arrTag[1]][str_replace('pageimage_', '', $arrTag[0])];
        }

        return false;
    }

    /**
     * Get one image by offset
     * @param   int
     * @param   bool
     * @return  array|null
     */
    public static function getOne($intIndex=0, $blnInherit=true)
    {
        static::initialize();

        if (static::$blnInherited && !$blnInherit) {
            return null;
        }

        if (!isset(static::$arrImages[$intIndex])) {
            return null;
        }

        return static::$arrImages[$intIndex];
    }

    /**
     * Get multiple images by offset and length
     * @param   int
     * @param   int|null
     * @param   bool
     * @return  array
     */
    public static function getMultiple($intOffset=0, $intLength=null, $blnInherit=true)
    {
        static::initialize();

        if (static::$blnInherited && !$blnInherit) {
            return null;
        }

        return array_slice(static::$arrImages, $intOffset, $intLength);
    }

    /**
     * Search for the current page image
     * @param    int
     * @param    bool
     * @return    mixed
     */
    public static function getPageImage($blnMultipleImages, $intIndex=0, $intTotal=null, $blnInherit=true)
    {
        static::initialize();

        $intIndex = (int) $intIndex;

        if (null === static::$arrImages) {
            global $objPage;

            // Current page has an image
            if ($objPage->pageImage != '') {
                static::$arrImages[$intIndex] = static::parsePage($blnMultipleImages, $objPage, $intIndex, $intTotal);
            }

            // Walk the trail
            elseif ($blnInherit && count($objPage->trail)) {
                $objTrail = \Database::getInstance()->execute("SELECT * FROM tl_page WHERE id IN (" . implode(',', $objPage->trail) . ") ORDER BY id=" . implode(' DESC, id=', array_reverse($objPage->trail)) . " DESC");

                while ($objTrail->next()) {
                    if ($objTrail->pageImage != '') {
                        static::$arrImages[$intIndex] = static::parsePage($blnMultipleImages, $objTrail, $intIndex, $intTotal);
                        break;
                    }
                }
            }
        }

        return static::$arrImages[$intIndex];
    }

    /**
     * Parse the given page and return the image information
     * @param    Database_Result
     * @param    int
     * @return    array
     */
    protected static function parsePage($objPage, $intIndex = null, $intTotal = null)
    {
        $arrOptions = array();
        $arrOrder = array_filter(explode(',', $objPage->pageImageOrder));

        if (!empty($arrOrder)) {
            $arrOptions['order'] = Database::getInstance()->findInSet('id', $arrOrder);
        }

        $arrImages = array();
        $objImages = \FilesModel::findMultipleByIds(deserialize($objPage->pageImage, true), $arrOptions);

        if (null !== $objImages) {
            while ($objImages->next()) {

                $objFile = new \File($objImages->path, true);

                if (!$objFile->isGdImage) {
                    continue;
                }

                $arrImage = $objImages->row();
                $arrMeta = static::getMetaData($objImages->meta, $objPage->language);

                // Use the file name as title if none is given
                if ($arrMeta['title'] == '') {
                    $arrMeta['title'] = specialchars(str_replace('_', ' ', preg_replace('/^[0-9]+_/', '', $objFile->filename)));
                }

                $arrImage['alt'] = $objPage->pageImageAlt;
                $arrImage['imageUrl'] = $arrMeta['link'];
                $arrImage['caption'] = $arrMeta['caption'];

                if (null !== $objPage->getRelated('pageImageJumpTo')) {
                    $arrImage['hasLink'] = true;
                    $arrImage['title'] = ($objPage->pageImageTitle ?: ($arrMeta['title'] ?: ($objJumpTo->pageTitle ?: $objJumpTo->title)));
                    $arrImage['href'] = \Controller::generateFrontendUrl($objPage->getRelated('pageImageJumpTo')->row());
                }

                $arrImages[] = $arrImage;
            }
        }

        return $arrImages;
    }
}
