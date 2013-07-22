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

// Set up the sanitised target for the ul
$ulTarget = str_replace('/', '-', $this->folders['data']->relative);

?>
<ul class="nav nav-list collapse in" id="collapseFolder-<?php echo $ulTarget; ?>">
<?php if (isset($this->folders['children'])) :
	foreach ($this->folders['children'] as $folder) :
	// Get a sanitised name for the target
	$target = str_replace('/', '-', $folder['data']->relative); ?>
	<li id="<?php echo $target; ?>">
		<i class="icon-folder-2 pull-left" data-toggle="collapse" data-target="#collapseFolder-<?php echo $target; ?>"></i>
		<a href="index.php?option=com_tz_media&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $folder['data']->relative; ?>" target="folderframe">
			<?php echo $folder['data']->name; ?>
		</a>
		<?php echo $this->getFolderLevel($folder); ?>
	</li>
<?php endforeach;
endif; ?>
</ul>
