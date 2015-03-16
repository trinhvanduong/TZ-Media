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

JHtml::_('bootstrap.tooltip');

$user = JFactory::getUser();
$params = new JRegistry;
$dispatcher	= JDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_tz_media.file', &$this->_tmp_doc, &$params));
?>
		<tr>
			<td>
				<a  title="<?php echo $this->_tmp_doc->name; ?>">
					<?php  echo JHtml::_('image', $this->_tmp_doc->icon_16, $this->_tmp_doc->title, null, true, true) ? JHtml::_('image', $this->_tmp_doc->icon_16, $this->_tmp_doc->title, array('width' => 16, 'height' => 16), true) : JHtml::_('image', 'media/con_info.png', $this->_tmp_doc->title, array('width' => 16, 'height' => 16), true);?> </a>
			</td>
			<td class="description"  title="<?php echo $this->_tmp_doc->name; ?>">
				<?php echo $this->_tmp_doc->title; ?>
			</td>
			<td>&#160;

			</td>
			<td class="filesize">
				<?php echo JHtml::_('number.bytes', $this->_tmp_doc->size); ?>
			</td>
		<?php if ($user->authorise('core.delete', 'com_tz_media')):?>
			<td>
				<a class="delete-item" target="_top" href="index.php?option=com_tz_media&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JSession::getFormToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_doc->name; ?>" rel="<?php echo $this->_tmp_doc->name; ?>"><i class="icon-remove hasTooltip" title="<?php echo JText::_('JACTION_DELETE');?>"></i></a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_doc->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_tz_media.file', &$this->_tmp_doc, &$params));
?>
