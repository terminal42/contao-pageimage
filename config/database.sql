-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_page`
-- 

CREATE TABLE `tl_page` (
  `pageImage` blob NULL,
  `pageImageOrder` text NULL,
  `pageImageJumpTo` varchar(255) NOT NULL default '',
  `pageImageAlt` varchar(255) NOT NULL default '',
  `pageImageTitle` varchar(255) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `inheritPageImage` char(1) NOT NULL default '',
  `slider_autostart` char(1) NOT NULL default '1',
  `slider_randomOrder` char(1) NOT NULL default '0',
  `slider_interval` int(6) unsigned NOT NULL default '0',
  `slider_animationDuration` int(6) unsigned NOT NULL default '0',
  `slider_transition` int(2) unsigned NOT NULL default '1',
  `slider_interval` int(10) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

