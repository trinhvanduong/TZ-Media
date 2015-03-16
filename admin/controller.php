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

/**
 * Media Manager Component Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_tz_media
 * @since       1.5
 */
class TZ_MediaController extends JControllerLegacy
{
    public function __construct($config=array()){
        $this -> input  = JFactory::getApplication() -> input;
        parent::__construct($config);

        // If the joomla's version is more than or equal to 3.0
        if(!COM_TZ_MEDIA_JVERSION_COMPARE){
            JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_media/libraries/cms/html');
            tzmediaimport('cms/html/sidebar');

            $doc    = JFactory::getDocument();
            //Add Script to the header
            $doc -> addScript(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/jui/js/jquery.min.js');
            $doc -> addScript(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/jui/js/jquery-noconflict.js');
            $doc -> addScript(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/jui/js/bootstrap.min.js');
            $doc -> addScript(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/jui/js/chosen.jquery.min.js');
            $doc -> addScript(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/jui/js/jquery.ui.core.min.js');
            $doc -> addScript(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/jui/js/jquery.ui.sortable.min.js');
            $doc -> addScript(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/jui/js/sortablelist.js');
            $doc -> addScript(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/js/template.js');

            $doc -> addStyleSheet(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/jui/css/chosen.css');
//                    $doc -> addStyleSheet(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/jui/css/bootstrap.min.css');
            //        $doc -> addStyleSheet(COM_TZ_MEDIA_ADMIN_HOST_PATH.'/css/template.css');
            $doc -> addCustomTag('<link href="'.COM_TZ_MEDIA_ADMIN_HOST_PATH.'/css/template.css'.
                '" rel="stylesheet" type="text/css"/>');
        }
    }
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		JPluginHelper::importPlugin('content');
		$vName = $this->input->get('view', 'media');
		switch ($vName)
		{
			case 'images':
				$vLayout = $this->input->get('layout', 'default');
				$mName = 'manager';

				break;

			case 'imagesList':
				$mName = 'list';
				$vLayout = $this->input->get('layout', 'default');

				break;

			case 'mediaList':
				$app	= JFactory::getApplication();
				$mName = 'list';
				$vLayout = $app->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

				break;

			case 'media':
			default:
				$vName = 'media';
				$vLayout = $this->input->get('layout', 'default');
				$mName = 'manager';
				break;
		}

		$document = JFactory::getDocument();
		$vType    = $document->getType();

		// Get/Create the view
		$view = $this->getView($vName, $vType);
		$view->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.'/views/'.strtolower($vName).'/tmpl');

		// Get/Create the model
		if ($model = $this->getModel($mName)) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();

		return $this;
	}

	public function ftpValidate()
	{
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
	}
}
