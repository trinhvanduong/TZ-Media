<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

define('COM_TZ_MEDIA','com_tz_media');
define ('COM_TZ_MEDIA_ADMIN_HOST_PATH', JURI::base(true).'/components/'.COM_TZ_MEDIA);
define ('COM_TZ_MEDIA_LIBRARIES', dirname(dirname(dirname(__FILE__.'/libraries'))));
define ('COM_TZ_MEDIA_ADMIN_PATH', dirname(dirname(dirname(__FILE__))));
define ('COM_TZ_MEDIA_JVERSION_COMPARE', version_compare(JVERSION,'3.0','ge'));

if(!COM_TZ_MEDIA_JVERSION_COMPARE && !DIRECTORY_SEPARATOR){
    define('DIRECTORY_SEPARATOR','\\');
}

if(file_exists(JPATH_ADMINISTRATOR.'/components/com_tz_media/tz_portfolio.xml')){
    define('COM_TZ_MEDIA_VERSION',JFactory::getXML(JPATH_ADMINISTRATOR.'/components/com_tz_media/tz_portfolio.xml')->version);
}elseif(file_exists(JPATH_ADMINISTRATOR.'/components/com_tz_media/manifest.xml')){
    define('COM_TZ_MEDIA_VERSION',JFactory::getXML(JPATH_ADMINISTRATOR.'/components/com_tz_media/manifest.xml')->version);
}