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
?>
<form target="_parent" action="index.php?option=com_tz_media&amp;tmpl=index&amp;folder=<?php echo $this->state->folder; ?>" method="post" id="mediamanager-form" name="mediamanager-form">
	<div class="manager">
	<table class="table table-striped table-condensed">
	<thead>
		<tr>
			<th width="1%"><?php echo JText::_('JGLOBAL_PREVIEW'); ?></th>
			<th><?php echo JText::_('COM_TZ_MEDIA_NAME'); ?></th>
			<th width="15%"><?php echo JText::_('COM_TZ_MEDIA_PIXEL_DIMENSIONS'); ?></th>
			<th width="8%"><?php echo JText::_('COM_TZ_MEDIA_FILESIZE'); ?></th>
		<?php if ($user->authorise('core.delete', 'com_tz_media')):?>
			<th width="8%"><?php echo JText::_('JACTION_DELETE'); ?></th>
		<?php endif;?>
		</tr>
	</thead>
	<tbody>
		<?php echo $this->loadTemplate('up'); ?>

		<?php for ($i = 0, $n = count($this->folders); $i < $n; $i++) :
			$this->setFolder($i);
			echo $this->loadTemplate('folder');
		endfor; ?>

		<?php for ($i = 0, $n = count($this->documents); $i < $n; $i++) :
			$this->setDoc($i);
			echo $this->loadTemplate('doc');
		endfor; ?>

		<?php for ($i = 0, $n = count($this->images); $i < $n; $i++) :
			$this->setImage($i);
			echo $this->loadTemplate('img');
		endfor; ?>

	</tbody>
	</table>
	<input type="hidden" name="task" value="list" />
	<input type="hidden" name="username" value="" />
	<input type="hidden" name="password" value="" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
