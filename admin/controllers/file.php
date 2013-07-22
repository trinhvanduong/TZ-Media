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
	/*
	 * The folder we are uploading into
	 */
	protected $folder = '';

	/**
	 * Used as a callback for array_map, turns the multi-file input array into a sensible array of files
	 * Also, removes illegal characters from the 'name' and sets a 'filepath' as the final destination of the file
	 *
	 * @param	string	- file name			($files['name'])
	 * @param	string	- file type			($files['type'])
	 * @param	string	- temporary name	($files['tmp_name'])
	 * @param	string	- error info		($files['error'])
	 * @param	string	- file size			($files['size'])
	 *
	 * @return	array
	 * @access	protected
	 */
	protected function reformatFilesArray($name, $type, $tmp_name, $error, $size)
	{
		$name = JFile::makeSafe($name);
		return array(
			'name'		=> $name,
			'type'		=> $type,
			'tmp_name'	=> $tmp_name,
			'error'		=> $error,
			'size'		=> $size,
			'filepath'	=> JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_TZ_MEDIA_BASE, $this->folder, $name)))
		);
	}

	/**
	 * Check that the user is authorized to perform this action
	 *
	 * @param   string   $action - the action to be peformed (create or delete)
	 *
	 * @return  boolean
	 * @access  protected
	 */
	protected function authoriseUser($action)
	{
		if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_tz_media'))
		{
			// User is not authorised
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));
			return false;
		}

		return true;
	}

	/**
	 * Deletes paths from the current path
	 *
	 */
	public function delete()
	{
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get some data from the request
		$tmpl	= $this->input->get('tmpl');
		$paths	= $this->input->get('rm', array(), 'array');
		$folder = $this->input->get('folder', '', 'path');

		$redirect = 'index.php?option=com_tz_media&folder=' . $folder;
		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component';
		}
		$this->setRedirect($redirect);

		// Nothing to delete
		if (empty($paths))
		{
			return true;
		}

		// Authorize the user
		if (!$this->authoriseUser('delete'))
		{
			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		JPluginHelper::importPlugin('content');
		$dispatcher	= JEventDispatcher::getInstance();

		$ret = true;
		foreach ($paths as $path)
		{

			if ($path !== JFile::makeSafe($path))
			{
				// filename is not safe
				$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
				JError::raiseWarning(100, JText::sprintf('COM_TZ_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_TZ_MEDIA_BASE))));
				continue;
			}

			$fullPath = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_TZ_MEDIA_BASE, $folder, $path)));
			$object_file = new JObject(array('filepath' => $fullPath));
			if (is_file($fullPath))
			{
				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger('onContentBeforeDelete', array('com_tz_media.file', &$object_file));
				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					JError::raiseWarning(100, JText::plural('COM_TZ_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
					continue;
				}

				$ret &= JFile::delete($fullPath);

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger('onContentAfterDelete', array('com_tz_media.file', &$object_file));
				$this->setMessage(JText::sprintf('COM_TZ_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_TZ_MEDIA_BASE))));
			}
			elseif (is_dir($fullPath))
			{
				$contents = JFolder::files($fullPath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
				if (empty($contents))
				{
					// Trigger the onContentBeforeDelete event.
					$result = $dispatcher->trigger('onContentBeforeDelete', array('com_tz_media.folder', &$object_file));
					if (in_array(false, $result, true))
					{
						// There are some errors in the plugins
						JError::raiseWarning(100, JText::plural('COM_TZ_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
						continue;
					}

					$ret &= JFolder::delete($fullPath);

					// Trigger the onContentAfterDelete event.
					$dispatcher->trigger('onContentAfterDelete', array('com_tz_media.folder', &$object_file));
					$this->setMessage(JText::sprintf('COM_TZ_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_TZ_MEDIA_BASE))));
				}
				else
				{
					// This makes no sense...
					JError::raiseWarning(100, JText::sprintf('COM_TZ_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', substr($fullPath, strlen(COM_TZ_MEDIA_BASE))));
				}
			}
		}

		return $ret;
	}

    function upload(){
        $this->folder = $this->input->get('folder', '', 'path');
        $params = JComponentHelper::getParams('com_tz_media');

        require_once JPATH_COMPONENT_ADMINISTRATOR
            .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'UploadHandler.php';
        $uploadHandler  = new UploadHandler(array(
            'upload_url' => implode('/',array(COM_TZ_MEDIA_BASEURL,$this ->folder)).'/',
            'upload_dir' => implode(DIRECTORY_SEPARATOR, array(COM_TZ_MEDIA_BASE, $this->folder)).DIRECTORY_SEPARATOR,
            'max_file_size' => $params -> get('upload_maxsize',10)*1024*1024,
            'create_thumbnail' => false,
            'accept_file_types' => '/\.'.str_replace(',','|',$params -> get('upload_extensions','mp3,mp4,ogg,webm,wav,flv')).'+$/i'
        ));
        die();
    }
}
