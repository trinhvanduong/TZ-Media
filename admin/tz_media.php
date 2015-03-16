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

defined('_JEXEC') or die;

$input  = JFactory::getApplication()->input;
$user   = JFactory::getUser();
$asset  = $input->get('asset');
$author = $input->get('author');

// Access check.
if (!$user->authorise('core.manage', 'com_tz_media')
	&&	(!$asset or (
			!$user->authorise('core.edit', $asset)
		&&	!$user->authorise('core.create', $asset)
		&& 	count($user->getAuthorisedCategories($asset, 'core.create')) == 0)
		&&	!($user->id == $author && $user->authorise('core.edit.own', $asset))))
{
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

$params = JComponentHelper::getParams('com_tz_media');

include_once dirname(__FILE__) . '/libraries/core/defines.php';
include_once dirname(__FILE__) . '/libraries/core/tzmedia.php';

// Load the helper class
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tz_media.php';

// Set the path definitions
$popup_upload = $input->get('pop_up', null);
$path = 'file_path';

$view = $input->get('view');
if (substr(strtolower($view), 0, 6) == 'images' || $popup_upload == 1)
{
	$path = 'image_path';
}

define('COM_TZ_MEDIA_ROOT','media/tz_media');
define('COM_TZ_MEDIA_BASE',    JPATH_ROOT . '/'.COM_TZ_MEDIA_ROOT );
define('COM_TZ_MEDIA_BASEURL', JURI::root() . COM_TZ_MEDIA_ROOT);
//define('COM_TZ_MEDIA_BASE',    JPATH_ROOT . '/' . $params->get($path, 'images'));
//define('COM_TZ_MEDIA_BASEURL', JURI::root() . $params->get($path, 'images'));

$controller	= JControllerLegacy::getInstance('TZ_Media', array('base_path' => JPATH_COMPONENT_ADMINISTRATOR));
$controller->execute($input->get('task'));
$controller->redirect();
