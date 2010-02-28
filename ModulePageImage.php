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
 * @author     Andreas Schempp <andreas@schempp.ch
 * @license    http://opensource.org/licenses/lgpl-3.0.html
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
		
		// Current page has an image
		if (strlen($objPage->pageImage))
		{
			$this->Template->src = $objPage->pageImage;
			
			if ($objPage->pageImageJumpTo)
			{
				$objJumpTo = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
											->limit(1)
											->execute($objPage->pageImageJumpTo);
				
				if ($objJumpTo->numRows)
				{
					$this->Template->hasLink = true;
					$this->Template->title = $objJumpTo->pageTitle ? $objJumpTo->pageTitle : $objJumpTo->title;
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
				if (strlen($objTrail->pageImage))
				{
					$this->Template->src = $objTrail->pageImage;
					
					if ($objTrail->pageImageJumpTo)
					{
						$objJumpTo = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
													->limit(1)
													->execute($objTrail->pageImageJumpTo);
						
						if ($objJumpTo->numRows)
						{
							$this->Template->hasLink = true;
							$this->Template->title = $objJumpTo->pageTitle ? $objJumpTo->pageTitle : $objJumpTo->title;
							$this->Template->href = $this->generateFrontendUrl($objJumpTo->row());
						}
					}
					
					return;
				}
			}
		}
	}
}

