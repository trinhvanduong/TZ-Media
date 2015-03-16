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
$user = JFactory::getUser();
$params = new JRegistry;
$dispatcher	= JDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_tz_media.file', &$this->_tmp_doc, &$params));
?>
    <li class="imgOutline thumbnail height-80 width-80 center">
        <a class="img-preview" href="javascript:ImageManager.populateFields('<?php echo $this->_tmp_doc->path_relative; ?>')" title="<?php echo $this->_tmp_doc->title; ?>" >
        <div class="height-50">
<!--            <a style="display: block; width: 100%; height: 100%" title="--><?php //echo $this->_tmp_doc->name; ?><!--" >-->
                <?php echo JHtml::_('image', $this->_tmp_doc->icon_32, $this->_tmp_doc->name, null, true, true) ? JHtml::_('image', $this->_tmp_doc->icon_32, $this->_tmp_doc->title, null, true) : JHtml::_('image', 'media/con_info.png', $this->_tmp_doc->name, null, true); ?>
        </div>
        <div class="small" title="<?php echo $this->_tmp_doc->name; ?>" >
            <?php echo JHtml::_('string.truncate', $this->_tmp_doc->name, 10, false); ?>
        </div>
            </a>
    </li>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_tz_media.file', &$this->_tmp_doc, &$params));
?>
