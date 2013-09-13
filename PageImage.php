<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
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
 * @copyright  Andreas Schempp 2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id: $
 */


class PageImage extends Frontend
{

	/**
	 * Current object instance (Singleton)
	 * @var object
	 */
	protected static $objInstance;

	/**
	 * Cache
	 */
	protected $arrCache = null;


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
	}


	/**
	 * Instantiate the class
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		if (!is_object(self::$objInstance))
		{
			self::$objInstance = new PageImage();
		}

		return self::$objInstance;
	}


	/**
	 * Replace "pageimage" inserttag
	 * @param string
	 * @return mixed
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
				$arrImage = $this->getPageImage(false, $arrTag[1]);

				if ($arrImage === false)
				{
					return '';
				}
			
				return $arrImage[str_replace('pageimage_', '', $arrTag[0])];
			
			default:
				return false;
		}
	}


	/**
	 * Search for the current page image
	 * @param	int
	 * @param	bool
	 * @return	mixed
	 */
	public function getPageImage($blnMultipleImages, $intIndex=0, $intTotal=null, $blnInherit=true)
	{
		$intIndex = (int)$intIndex;
		
		if (is_null($this->arrCache[$intIndex]))
		{
			global $objPage;
			
			$this->arrCache[$intIndex] = false;
			
			// Current page has an image
			if ($objPage->pageImage != '')
			{
				$this->arrCache[$intIndex] = $this->parsePage($blnMultipleImages, $objPage, $intIndex, $intTotal);
			}
			
			// Walk the trail
			elseif ($blnInherit && count($objPage->trail))
			{
				$objTrail = $this->Database->execute("SELECT * FROM tl_page WHERE id IN (" . implode(',', $objPage->trail) . ") ORDER BY id=" . implode(' DESC, id=', array_reverse($objPage->trail)) . " DESC");
				
				while( $objTrail->next() )
				{
					if ($objTrail->pageImage != '')
					{
						$this->arrCache[$intIndex] = $this->parsePage($blnMultipleImages, $objTrail, $intIndex, $intTotal);
						break;
					}
				}
			}
		}
		
		return $this->arrCache[$intIndex];
	}


	/**
	 * Parse the given page and return the image information
	 * @param	Database_Result
	 * @param	int
	 * @return	array
	 */
	protected function parsePage($blnMultipleImages, $objPage, $intIndex = null, $intTotal = null)
	{
        $arrOptions = array();
        $arrOrder = array_filter(explode(',', $objPage->pageImageOrder));

        if (!empty($arrOrder)) {
            $arrOptions['order'] = Database::getInstance()->findInSet('id', $arrOrder);
        }

        $arrJumpTo = array();
        if ($objPage->pageImageJumpTo)
        {
            $objJumpTo = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->limit(1)->execute($objPage->pageImageJumpTo);

            if ($objJumpTo->numRows)
            {
                $arrJumpTo['title'] = $objPage->pageImageTitle ? $objPage->pageImageTitle : ($objJumpTo->pageTitle ? $objJumpTo->pageTitle : $objJumpTo->title);
                $arrJumpTo['href'] = $this->generateFrontendUrl($objJumpTo->row());
            }
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
                if ($arrMeta['title'] == '')
                {
                    $arrMeta['title'] = specialchars(str_replace('_', ' ', preg_replace('/^[0-9]+_/', '', $objFile->filename)));
                }
                $arrImage['imageUrl'] = $arrMeta['link'];
                $arrImage['caption'] = $arrMeta['caption'];

                $arrImage['alt'] = $objPage->pageImageAlt;
                $arrImage['title'] = $arrJumpTo['title'];
                $arrImage['href'] = $arrJumpTo['href'];

                $arrImages[] = $arrImage;
            }
        }

        if($blnMultipleImages) {

            // number of elements limited
            if(isset($intTotal) && $intTotal > 0) {

                // specific index selected
                if(isset($intIndex) && $intIndex > 0) {
                    return array_slice($arrImages, $intIndex, $intTotal);
                } else {
                    return array_slice($arrImages, $intTotal);
                }
            }

        } else {
            $intIndex = $intIndex < count($arrImages) ? $intIndex : 0;
            return $arrImages[$intIndex];
        }


		return $arrImages;
	}

}

