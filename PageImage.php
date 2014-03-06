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
     * Replace "pageimage" inserttag
     * @param   string
     * @return  mixed
     */
    public static function replaceTags($strTag)
    {
        $arrTag = trimsplit('::', $strTag);

        switch($arrTag[0])
        {
            case 'pageimage':
                $arrTag[0] = 'pageimage_src';
                // Do NOT add a break;

            case 'pageimage_alt':
            case 'pageimage_title':
            case 'pageimage_href':

                global $objPage;
                $arrImage = static::findOne($objPage, (int) $arrTag[1]);
                $strKey = str_replace('pageimage_', '', $arrTag[0]);

                if (null === $arrImage || !isset($arrImages[$strKey])) {
                    return '';
                }

                return $arrImages[$intIndex][$strKey];
        }

        return false;
    }

    /**
     * Get one image by offset
     * @param   \PageModel
     * @param   int
     * @param   bool
     * @return  array|null
     */
    public static function getOne(\PageModel $objPage, $intIndex=0, $blnInherit=true)
    {
        $arrImages = static::findForPage($objPage, $blnInherit);

        if (null === $arrImages || !isset($arrImages[$intIndex])) {
            return null;
        }

        return $arrImages[$intIndex];
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
        $arrImages = static::findForPage($objPage, $blnInherit);

        if (null === $arrImages || !isset($arrImages[$intOffset])) {
            return array();
        }

        return array_slice($arrImages, $intOffset, $intLength);
    }


    /**
     * Find images for given page
     * @param   \PageModel
     * @return  array
     */
    protected static function findForPage(\PageModel $objPage, $blnInherit=true)
    {
        if (!isset(static::$arrImages[$objPage->id])) {

            static::$arrImages[$objPage->id] = false;

            $arrImages = static::parsePage($objPage);

            if (!empty($arrImages)) {
                static::$arrImages[$objPage->id] = array(
                    'images'    => $arrImages,
                    'inherited' => false
                );
            }

            // Walk the trail
            else {
                $objPage->loadDetails();
                $objTrails = \PageModel::findMultipleByIds($objPage->trail, array('order'=>\Database::getInstance()->findInSet('id', array_reverse($objPage->trail))));

                if (null !== $objTrails) {
                    foreach ($objTrails as $objTrail) {
                        $arrImages = static::parsePage($objTrail);

                        if (!empty($arrImages)) {
                            static::$arrImages[$objPage->id] = array(
                                'images'    => $arrImages,
                                'inherited' => true
                            );

                            break;
                        }
                    }
                }
            }
        }

        if (static::$arrImages[$objPage->id] === false || (!$blnInherit && static::$arrImages[$objPage->id]['inherited'])) {
            return null;
        }

        return static::$arrImages[$objPage->id]['images'];
    }

    /**
     * Parse the given page and return the image information
     * @param    \PageModel
     * @return   array
     */
    protected static function parsePage(\PageModel $objPage)
    {
        if ($objPage->pageImage == '') {
            return array();
        }

        $arrImages = array();
        $objImages = \FilesModel::findMultipleByIds(deserialize($objPage->pageImage, true));

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

            $arrOrder = deserialize($objPage->pageImageOrder);

            if (!empty($arrOrder) && is_array($arrOrder))
            {
            	// Remove all values
            	$arrOrder = array_map(function(){}, array_flip($arrOrder));

            	// Move the matching elements to their position in $arrOrder
            	foreach ($arrImages as $k=>$v)
            	{
            		if (array_key_exists($v['uuid'], $arrOrder))
            		{
            			$arrOrder[$v['uuid']] = $v;
            			unset($arrImages[$k]);
            		}
            	}

            	// Append the left-over images at the end
            	if (!empty($arrImages))
            	{
            		$arrOrder = array_merge($arrOrder, array_values($arrImages));
            	}

            	// Remove empty (unreplaced) entries
            	$arrImages = array_values(array_filter($arrOrder));
            	unset($arrOrder);
            }
        }

        return $arrImages;
    }
}
