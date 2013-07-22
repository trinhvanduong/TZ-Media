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

?>
<?php if (count($this->images) > 0 || count($this->folders) > 0 || count($this->documents) > 0) { ?>
<ul class="manager thumbnails">
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
		echo $this->loadTemplate('image');
	endfor; ?>
</ul>
<?php } else { ?>
	<div id="media-noimages">
		<div class="alert alert-info"><?php echo JText::_('COM_TZ_MEDIA_NO_IMAGES_FOUND'); ?></div>
	</div>
<?php } ?>
