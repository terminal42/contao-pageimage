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
     * Current object instance (Singleton)
     * @var object
     */
    protected static $objInstance;

    /**
     * Images
     * @var array
     */
    protected $arrImages;

    /**
     * Image has been inherited
     * @var bool
     */
    protected $blnInherited;


    /**
     * Prevent cloning of the object (Singleton)
     */
    final private function __clone() {}


    /**
     * Prevent direct instantiation (Singleton)
     */
    protected function __construct()
    {
        parent::__construct();

        global $objPage;

        // Current page has an image
        if ($objPage->pageImage != '') {
            $this->arrImages = $this->parsePage($objPage, $intIndex, $intTotal);
            $this->blnInherited = false;
        }

        // Walk the trail
        else {
            $this->arrImages = array();
            $this->blnInherited = true;

            $objTrail = \PageModel::findMultipleByIds($objPage->trail, array('order'=>Database::getInstance()->findInSet('id', array_reverse($objPage->trail))));

            if (null !== $objTrail) {
                while ($objTrail->next()) {
                    if ($objTrail->pageImage != '') {
                        $this->arrImages = $this->parsePage($objTrail, $intIndex, $intTotal);
                        break;
                    }
                }
            }
        }
    }


    /**
     * Instantiate the class
     * @return object
     */
    public static function getInstance()
    {
        if (!is_object(self::$objInstance)) {
            self::$objInstance = new PageImage();
        }

        return self::$objInstance;
    }

    /**
     * Replace "pageimage" inserttag
     * @param   string
     * @return  mixed
     */
    public function replaceTags($strTag)
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
                if (!isset($this->arrImages[$arrTag[1]])) {
                    return '';
                }

                return $this->arrImages[$arrTag[1]][str_replace('pageimage_', '', $arrTag[0])];
        }

        return false;
    }

    /**
     * Get one image by offset
     * @param   int
     * @param   bool
     * @return  array|null
     */
    public function getOne($intIndex=0, $blnInherit=true)
    {
        if ($this->blnInherited && !$blnInherit) {
            return null;
        }

        if (!isset($this->arrImages[$intIndex])) {
            return null;
        }

        return $this->arrImages[$intIndex];
    }

    /**
     * Get multiple images by offset and length
     * @param   int
     * @param   int|null
     * @param   bool
     * @return  array
     */
    public function getMultiple($intOffset=0, $intLength=null, $blnInherit=true)
    {
        if ($this->blnInherited && !$blnInherit) {
            return null;
        }

        return array_slice($this->arrImages, $intOffset, $intLength);
    }

    /**
     * Search for the current page image
     * @param    int
     * @param    bool
     * @return    mixed
     */
    public function getPageImage($blnMultipleImages, $intIndex=0, $intTotal=null, $blnInherit=true)
    {
        $intIndex = (int) $intIndex;

        if (null === $this->arrImages) {
            global $objPage;

            // Current page has an image
            if ($objPage->pageImage != '') {
                $this->arrImages[$intIndex] = $this->parsePage($blnMultipleImages, $objPage, $intIndex, $intTotal);
            }

            // Walk the trail
            elseif ($blnInherit && count($objPage->trail)) {
                $objTrail = \Database::getInstance()->execute("SELECT * FROM tl_page WHERE id IN (" . implode(',', $objPage->trail) . ") ORDER BY id=" . implode(' DESC, id=', array_reverse($objPage->trail)) . " DESC");

                while ($objTrail->next()) {
                    if ($objTrail->pageImage != '') {
                        $this->arrImages[$intIndex] = $this->parsePage($blnMultipleImages, $objTrail, $intIndex, $intTotal);
                        break;
                    }
                }
            }
        }

        return $this->arrImages[$intIndex];
    }

    /**
     * Parse the given page and return the image information
     * @param    Database_Result
     * @param    int
     * @return    array
     */
    protected function parsePage($objPage, $intIndex = null, $intTotal = null)
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
                $arrMeta = $this->getMetaData($objImages->meta, $objPage->language);

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
                    $arrImage['href'] = $this->generateFrontendUrl($objPage->getRelated('pageImageJumpTo')->row());
                }

                $arrImages[] = $arrImage;
            }
        }

        return $arrImages;
    }
}
