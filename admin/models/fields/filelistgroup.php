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

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
JFormHelper::loadFieldClass('list');

class JFormFieldFileListGroup extends JFormFieldList
{
    protected $type = 'FileListGroup';

    protected function getOptions()
	{
        $element    = $this -> element;
        $attribute   = $element -> attributes();
        
        $options = array();

		// Initialize some field attributes.
		$filter = (string) $this->element['filter'];
		$exclude = (string) $this->element['exclude'];
		$stripExt = (string) $this->element['stripext'];
		$hideNone = (string) $this->element['hide_none'];
		$hideDefault = (string) $this->element['hide_default'];

		// Get the path in which to search for file options.
		$path = (string) $this->element['directory'];
        if(!$path){
            $path   = COM_TZ_MEDIA_ROOT;
        }

		if (!is_dir($path))
		{
			$path = JPATH_ROOT . '/' . $path;
		}


		// Prepend some default options based on field attributes.
		if (!$hideNone)
		{
			$options[] = JHtml::_('select.option', '-1', JText::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}
		if (!$hideDefault)
		{
			$options[] = JHtml::_('select.option', '', JText::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}

		// Get a list of files in the search path with the given filter.
		$files = JFolder::files($path, $filter);

		// Build the options list from the list of files.
		if (is_array($files))
		{
            // Start group:
            $options[]  = JHtml::_('select.optgroup','Root');
			foreach ($files as $file)
			{

                if(is_file($path.'/'.$file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html'){
                    // Check to see if the file is in the exclude mask.
                    if ($exclude)
                    {
                        if (preg_match(chr(1) . $exclude . chr(1), $file))
                        {
                            continue;
                        }
                    }

                    // If the extension is to be stripped, do it.
                    if ($stripExt)
                    {
                        $file = JFile::stripExt($file);
                    }

                    $options[] = JHtml::_('select.option', $this -> element['directory'].'/'.$file, $file);
                }
			}
            
            // Finish group:
            $options[]  = JHtml::_('select.optgroup',JText::_('JGLOBAL_ROOT'));
		}


        $folderList = false;
        if(JFolder::exists($path)){
            // Get the list of files and folders from the given folder
            $folderList = JFolder::listFolderTree($path,$this -> element['filter']);
        }


        // Iterate over the folders if they exist
		if ($folderList !== false) {
			foreach ($folderList as $folder){

                $fileslist  = JFolder::files($folder['fullname']);
                if($fileslist && count($fileslist)>1){


                    // Start group:
                    $options[]  = JHtml::_('select.optgroup',$folder['name']);

                    foreach($fileslist as $_file){
                        if (is_file($folder['fullname'].'/'.$_file) && substr($_file, 0, 1) != '.' && strtolower($_file) !== 'index.html') {
                            $options[] = JHtml::_('select.option', preg_replace('/^\//','',str_replace('\\','/',$folder['relname'].'/'.$_file)), $_file);
                        }
                    }
                    // Finish group:
                    $options[]  = JHtml::_('select.optgroup',$folder['name']);
                }
            }
        }

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
    }
    
}
 
