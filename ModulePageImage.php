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


class ModulePageImage extends Module
{
	protected $strTemplate = 'mod_pageimage';
	
	
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### PAGE IMAGE ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'typolight/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$strBuffer = parent::generate();
		
		if (!strlen($this->Template->src))
			return '';
			
		return $strBuffer;
	}
	
	
	protected function compile()
	{
		global $objPage;
		
		$arrSize = deserialize($this->imgSize);
		
		// Current page has an image
		if ($objPage->pageImage)
		{
			
			if (is_numeric($objPage->pageImage)) 
			{
                		$objFile = \FilesModel::findByPk($objPage->pageImage);
                		$objPage->pageImage = $objFile->path;
            		}
			$strImage = $this->getImage($objPage->pageImage, $arrSize[0], $arrSize[1], $arrSize[2]);
			
			$this->Template->src = $strImage;
			$this->Template->alt = $objPage->pageImageAlt;
			
			if (($imgSize = @getimagesize(TL_ROOT . '/' . $strImage)) !== false)
			{
				$this->Template->size = ' ' . $imgSize[3];
			}
			
			if ($objPage->pageImageJumpTo)
			{
				$objJumpTo = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->limit(1)->execute($objPage->pageImageJumpTo);
				
				if ($objJumpTo->numRows)
				{
					$this->Template->hasLink = true;
					$this->Template->title = $objPage->pageImageTitle ? $objPage->pageImageTitle : ($objJumpTo->pageTitle ? $objJumpTo->pageTitle : $objJumpTo->title);
					$this->Template->href = $this->generateFrontendUrl(array('id'=>$objJumpTo->id, 'alias'=>$objJumpTo->alias));
				}
			}
		}
		
		// Walk the trail
		elseif ($this->inheritPageImage && count($objPage->trail))
		{
			$objTrail = $this->Database->execute("SELECT * FROM tl_page WHERE id IN (" . implode(',', $objPage->trail) . ") ORDER BY id=" . implode(' DESC, id=', array_reverse($objPage->trail)) . " DESC");
			
			while( $objTrail->next() )
			{
				if ($objTrail->pageImage)
				{
					if (is_numeric($objTrail->pageImage)) 
					{
                        			$objFile = \FilesModel::findByPk($objTrail->pageImage);
                        			$objTrail->pageImage = $objFile->path;
                    			}
					$strImage = $this->getImage($objTrail->pageImage, $arrSize[0], $arrSize[1], $arrSize[2]);
					
					$this->Template->src = $strImage;
		                    	$objPage->pageImage = $strImage; // pass on objPage
					$this->Template->alt = $objTrail->pageImageAlt;
					
					if (($imgSize = @getimagesize(TL_ROOT . '/' . $strImage)) !== false)
					{
						$this->Template->size = ' ' . $imgSize[3];
					}
					
					if ($objTrail->pageImageJumpTo)
					{
						$objJumpTo = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->limit(1)->execute($objTrail->pageImageJumpTo);
						
						if ($objJumpTo->numRows)
						{
							$this->Template->hasLink = true;
							$this->Template->title = $objTrail->pageImageTitle ? $objTrail->pageImageTitle : ($objJumpTo->pageTitle ? $objJumpTo->pageTitle : $objJumpTo->title);
							$this->Template->href = $this->generateFrontendUrl($objJumpTo->row());
						}
					}
					
					return;
				}
			}
		}
	}
}

