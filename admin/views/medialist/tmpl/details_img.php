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
$dispatcher->trigger('onContentBeforeDisplay', array('com_tz_media.file', &$this->_tmp_img, &$params));
?>
		<tr>
			<td>
				<a class="img-preview" href="<?php echo COM_TZ_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>"><?php echo JHtml::_('image', COM_TZ_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative, JText::sprintf('COM_TZ_MEDIA_IMAGE_TITLE', $this->_tmp_img->title, JHtml::_('number.bytes', $this->_tmp_img->size)), array('width' => $this->_tmp_img->width_16, 'height' => $this->_tmp_img->height_16)); ?></a>
			</td>
			<td class="description">
				<a href="<?php echo  COM_TZ_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>" rel="preview"><?php echo $this->escape($this->_tmp_img->title); ?></a>
			</td>
			<td>
				<?php echo JText::sprintf('COM_TZ_MEDIA_IMAGE_DIMENSIONS', $this->_tmp_img->width, $this->_tmp_img->height); ?>
			</td>
			<td class="filesize">
				<?php echo JHtml::_('number.bytes', $this->_tmp_img->size); ?>
			</td>
		<?php if ($user->authorise('core.delete', 'com_tz_media')):?>
			<td>
				<a class="delete-item" target="_top" href="index.php?option=com_tz_media&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JSession::getFormToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_img->name; ?>" rel="<?php echo $this->_tmp_img->name; ?>"><i class="icon-remove hasTooltip" title="<?php echo JText::_('JACTION_DELETE');?>"></i></a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_img->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_tz_media.file', &$this->_tmp_img, &$params));
?>
