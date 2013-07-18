<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *
 * PHP version 5
 * @copyright  terminal42 gmbh 2009-2013
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Kamil Kuźmiński <kamil.kuzminski@terminal42.ch>
 * @license    LGPL
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
			$strPath = $objPage->pageImage;

			// Contao 3 mode
			if (is_numeric($objPage->pageImage))
			{
				$objImage = \FilesModel::findByPk($objPage->pageImage);

				if ($objImage !== null)
				{
					$strPath = $objImage->path;
				}
			}

			$strImage = $this->getImage($strPath, $arrSize[0], $arrSize[1], $arrSize[2]);

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
					$strPath = $objTrail->pageImage;

					// Contao 3 mode
					if (is_numeric($objTrail->pageImage))
					{
						$objImage = \FilesModel::findByPk($objTrail->pageImage);

						if ($objImage !== null)
						{
							$strPath = $objImage->path;
						}
					}

					$strImage = $this->getImage($strPath, $arrSize[0], $arrSize[1], $arrSize[2]);

					$this->Template->src = $strImage;
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

