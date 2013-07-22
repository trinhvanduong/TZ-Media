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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class TZ_MediaControllerFile extends JControllerLegacy
{
    
	function upload()
	{
		$params = JComponentHelper::getParams('com_tz_media');
		// Check for request forgeries
		if (!JSession::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		// Get the user
		$user  = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		JLog::addLogger(array('text_file' => 'upload.error.php'), JLog::ALL, array('upload'));

		// Get some data from the request
		$file   = JRequest::getVar('Filedata', '', 'files', 'array');
		$folder = $input->get('folder', '', 'path');
		$return = $input->post->get('return-url', null, 'base64');

		if (
			$_SERVER['CONTENT_LENGTH']>($params->get('upload_maxsize', 0) * 1024 * 1024) ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('upload_max_filesize'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('post_max_size'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('memory_limit'))* 1024 * 1024
		)
		{
			$response = array(
					'status' => '0',
					'error' => JText::_('COM_TZ_MEDIA_ERROR_WARNFILETOOLARGE')
			);
			echo json_encode($response);
			return;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			// The request is valid
			$err = null;

			$filepath = JPath::clean(COM_TZ_MEDIA_BASE . '/' . $folder . '/' . strtolower($file['name']));

			if (!TZ_MediaHelper::canUpload($file, $err))
			{
				JLog::add('Invalid: ' . $filepath . ': ' . $err, JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::_($err)
				);
				echo json_encode($response);
				return;
			}

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher	= JEventDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_tz_media.file', &$object_file));
			if (in_array(false, $result, true)) {
				// There are some errors in the plugins
				JLog::add('Errors before save: ' . $filepath . ' : ' . implode(', ', $object_file->getErrors()), JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::plural('COM_TZ_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
				);
				echo json_encode($response);
				return;
			}

			if (JFile::exists($filepath))
			{
				// File exists
				JLog::add('File exists: ' . $filepath . ' by user_id ' . $user->id, JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_TZ_MEDIA_ERROR_FILE_EXISTS')
				);
				echo json_encode($response);
				return;
			}
			elseif (!$user->authorise('core.create', 'com_tz_media'))
			{
				// File does not exist and user is not authorised to create
				JLog::add('Create not permitted: ' . $filepath . ' by user_id ' . $user->id, JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_TZ_MEDIA_ERROR_CREATE_NOT_PERMITTED')
				);
				echo json_encode($response);
				return;
			}

			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				JLog::add('Error on upload: ' . $filepath, JLog::INFO, 'upload');
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_TZ_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				echo json_encode($response);
				return;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_tz_media.file', &$object_file, true));
				JLog::add($folder, JLog::INFO, 'upload');
				$response = array(
					'status' => '1',
					'error' => JText::sprintf('COM_TZ_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_TZ_MEDIA_BASE)))
				);
				echo json_encode($response);
				return;
			}
		}
		else
		{
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_TZ_MEDIA_ERROR_BAD_REQUEST')
			);

			echo json_encode($response);
			return;
		}
	}
}
