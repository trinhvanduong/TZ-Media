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

class TZ_MediaViewImages extends JViewLegacy
{
	public function display($tpl = null)
	{
		$config = JComponentHelper::getParams('com_tz_media');
		$app	= JFactory::getApplication();
		$lang	= JFactory::getLanguage();
		$append = '';
        $document = JFactory::getDocument();

		JHtml::_('behavior.framework', true);
//		JHtml::_('script', 'media/popup-imagemanager.js', true, true);
        $document -> addCustomTag('<script src="'.JUri::base(true).'/components/com_tz_media/js/tzpopup-imagemanager.js'
            .'" type="text/javascript"></script>');
		JHtml::_('stylesheet', 'media/popup-imagemanager.css', array(), true);

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
                    ImageManager.refreshFrame();
                }
            });
        })'.'</script>');
        $document -> addCustomTag('<!--[if gte IE 8]><script src="'.JUri::base(true).'/components/com_tz_media/js/cors/jquery.xdr-transport.js'
            .'" type="text/javascript"></script><![endif]-->');

		if ($lang->isRTL()) {
			JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array(), true);
		}

		
		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->session = JFactory::getSession();
		$this->config = $config;
		$this->state = $this->get('state');
		$this->folderList = $this->get('folderList');
		$this->require_ftp = $ftp;

		parent::display($tpl);
	}
}
