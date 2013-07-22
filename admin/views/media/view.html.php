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

class TZ_MediaViewMedia extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$config = JComponentHelper::getParams('com_tz_media');

		$lang	= JFactory::getLanguage();

		$style = $app->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

		$document = JFactory::getDocument();

		JHtml::_('behavior.framework', true);

//		JHtml::_('script', 'media/mediamanager.js', true, true);

		JHtml::_('behavior.modal');
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			document.preview = SqueezeBox;
		});");

		JHtml::_('stylesheet', 'system/mootree.css', array(), true);
		if ($lang->isRTL()) :
			JHtml::_('stylesheet', 'media/mootree_rtl.css', array(), true);
		endif;


        $document -> addStyleSheet(JUri::base(true).'/components/com_tz_media/css/jquery.fileupload-ui.css');
        $document -> addStyleSheet(JUri::base(true).'/components/com_tz_media/css/tz_media.css');

//        JHtml::_('script', 'media/mediamanager.js', true, true);
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/tzmediamanager.js'
            .'" type="text/javascript"></script>');
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/vendor/jquery.ui.widget.js'
            .'" type="text/javascript"></script>');
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/tmpl.min.js'
            .'" type="text/javascript"></script>');
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/load-image.min.js'
            .'" type="text/javascript"></script>');
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/canvas-to-blob.min.js'
            .'" type="text/javascript"></script>');
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/jquery.iframe-transport.js'
            .'" type="text/javascript"></script>');
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/jquery.fileupload.js'
            .'" type="text/javascript"></script>');
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/jquery.fileupload-fp.js'
            .'" type="text/javascript"></script>');
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/jquery.fileupload-ui.js'
            .'" type="text/javascript"></script>');
        //        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/main.js'
        //            .'" type="text/javascript"></script>');
        $document -> addCustomTag('<script type="text/javascript">'
            .'// Initialize the jQuery File Upload widget:
        jQuery(function () {
            \'use strict\';
            jQuery(\'#uploadForm\').fileupload({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                url: \'index.php?option=com_tz_media&task=file.upload\',
                errorMsg:{
                    emptyFileMsg: \''.JText::_('COM_TZ_MEDIA_ERROR_EMPTY_FILE').'\',
                    maxNumberOfFilesMsg: \''.JText::_('COM_TZ_MEDIA_ERROR_MAX_NUMBER_OF_FILE').'\',
                    acceptFileTypesMsg: \''.JText::_('COM_TZ_MEDIA_ERROR_ACCEPT_FILE_TYPE').'\',
                    maxFileSize: \''.JText::_('COM_TZ_MEDIA_ERROR_MAX_FILE_SIZE').'\',
                    minFileSize: \''.JText::_('COM_TZ_MEDIA_ERROR_MIN_FILE_SIZE').'\'
                },
                maxFileSize: '.((int)$config -> get('upload_maxsize',10)*1024*1024).',
                acceptFileTypes: /\.'.str_replace(',','|',$config -> get('upload_extensions','mp3,mp4,ogg,webm,wav,flv'))
                                  .'+$/i,
                stop: function(){
                    var that = jQuery(this).data(\'blueimp-fileupload\') ||
                            jQuery(this).data(\'fileupload\'),
                        deferred = that._addFinishedDeferreds();
                    jQuery.when.apply(jQuery, that._getFinishedDeferreds());
                    that._transition(jQuery(this).find(\'.fileupload-progress\')).done(
                        function () {
                            jQuery(this).find(\'.progress\')
                                .attr(\'aria-valuenow\', \'0\')
                                .find(\'.bar\').css(\'width\', \'0%\');
                            jQuery(this).find(\'.progress-extended\').html(\'&nbsp;\');
                            deferred.resolve();
                        }
                    );
                    MediaManager.refreshFrame();
                }
            });
        })'.'</script>');
        $document -> addCustomTag('<!--[if gte IE 8]><script src="'.JUri::base(true).'/components/com_tz_media/js/cors/jquery.xdr-transport.js'
            .'" type="text/javascript"></script><![endif]-->');

		if (DIRECTORY_SEPARATOR == '\\')
		{
			$base = str_replace(DIRECTORY_SEPARATOR, "\\\\", COM_TZ_MEDIA_BASE);
		} else {
			$base = COM_TZ_MEDIA_BASE;
		}

		$js = "
			var basepath = '".$base."';
			var viewstyle = '".$style."';
		";
		$document->addScriptDeclaration($js);

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$session	= JFactory::getSession();
		$state		= $this->get('state');
		$this->session = $session;
		$this->config = &$config;
		$this->state = &$state;
		$this->require_ftp = $ftp;
		$this->folders_id = ' id="media-tree"';
		$this->folders = $this->get('folderTree');

		// Set the toolbar
		$this->addToolbar();

		parent::display($tpl);
		echo JHtml::_('behavior.keepalive');
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		$user = JFactory::getUser();

		// Set the titlebar text
		JToolbarHelper::title(JText::_('COM_TZ_MEDIA'), 'mediamanager.png');

		// Add a upload button
		if ($user->authorise('core.create', 'com_tz_media'))
		{
			$title = JText::_('JTOOLBAR_UPLOAD');
			$dhtml = "<button data-toggle=\"collapse\" data-target=\"#collapseUpload\" class=\"btn btn-small btn-success\">
						<i class=\"icon-plus icon-white\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'upload');
			JToolbarHelper::divider();
		}

		// Add a create folder button
		if ($user->authorise('core.create', 'com_tz_media'))
		{
			$title = JText::_('COM_TZ_MEDIA_CREATE_FOLDER');
			$dhtml = "<button data-toggle=\"collapse\" data-target=\"#collapseFolder\" class=\"btn btn-small\">
						<i class=\"icon-folder\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'folder');
			JToolbarHelper::divider();
		}

		// Add a delete button
		if ($user->authorise('core.delete', 'com_tz_media'))
		{
			$title = JText::_('JTOOLBAR_DELETE');
			$dhtml = "<button href=\"#\" onclick=\"MediaManager.submit('folder.delete')\" class=\"btn btn-small\">
						<i class=\"icon-remove\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'delete');
			JToolbarHelper::divider();
		}
		// Add a delete button
		if ($user->authorise('core.admin', 'com_tz_media'))
		{
			JToolbarHelper::preferences('com_tz_media');
			JToolbarHelper::divider();
		}
		JToolbarHelper::help('JHELP_CONTENT_MEDIA_MANAGER');
	}

	function getFolderLevel($folder)
	{
		$this->folders_id = null;
		$txt = null;
		if (isset($folder['children']) && count($folder['children'])) {
			$tmp = $this->folders;
			$this->folders = $folder;
			$txt = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}
		return $txt;
	}
}
