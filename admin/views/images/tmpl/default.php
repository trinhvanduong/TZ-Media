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

JHtml::_('formbehavior.chosen', 'select');

$user  = JFactory::getUser();
$input = JFactory::getApplication()->input;
$doc    = JFactory::getDocument();
$doc -> addCustomTag('<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
        <td class="error" colspan="2"><span class="label label-important">'.JText::_('COM_TZ_MEDIA_ERROR').'</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
        <td>
            <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
        </td>
        <td class="start">{% if (!o.options.autoUpload) { %}
            <button class="btn btn-primary">
                <i class="icon-upload icon-white"></i>
                <span>'.JText::_('COM_TZ_MEDIA_START').'</span>
            </button>
            {% } %}</td>
        {% } else { %}
        <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>'.JText::_('JCANCEL').'</span>
            </button>
            {% } %}</td>
    </tr>
    {% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
        <td></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        <td class="error" colspan="2"><span class="label label-important">'.JText::_('COM_TZ_MEDIA_ERROR').'</span> {%=file.error%}</td>
        {% } else { %}
        <td class="preview">{% if (file.thumbnail_url) { %}
            <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
        <td class="name">
            <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&\'gallery\'%}" download="{%=file.name%}">{%=file.name%}</a>
        </td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger">
                <i class="icon-trash icon-white"></i>
                <span>'.JText::_('COM_TZ_MEDIA_REMOVE').'</span>
            </button>
        </td>
    </tr>
    {% } %}
</script>
');

?>
<script type='text/javascript'>
var image_base_path = '<?php echo COM_TZ_MEDIA_ROOT;?>/';
</script>
<form action="index.php?option=com_tz_media&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>" class="form-horizontal" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true)?>
	</div>
	<div class="well">
		<div class="row">
			<div class="span9 control-group">
				<div class="control-label">
					<label class="control-label" for="folder"><?php echo JText::_('COM_TZ_MEDIA_DIRECTORY') ?></label>
				</div>
				<div class="controls">
					<?php echo $this->folderList; ?>
					<button class="btn" type="button" id="upbutton" title="<?php echo JText::_('COM_TZ_MEDIA_DIRECTORY_UP') ?>"><?php echo JText::_('COM_TZ_MEDIA_UP') ?></button>
				</div>
			</div>
			<div class="pull-right">
				<button class="btn btn-primary" type="button" onclick="<?php if ($this->state->get('field.id')):?>window.parent.jInsertFieldValue(document.id('f_url').value,'<?php echo $this->state->get('field.id');?>');<?php else:?>ImageManager.onok();<?php endif;?>window.parent.SqueezeBox.close();"><?php echo JText::_('COM_TZ_MEDIA_INSERT') ?></button>
				<button class="btn" type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL') ?></button>
			</div>
		</div>
	</div>

	<iframe id="imageframe" name="imageframe" src="index.php?option=com_tz_media&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>"></iframe>

	<div class="well">
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_url"><?php echo JText::_('COM_TZ_MEDIA_IMAGE_URL') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_url" value="" />
				</div>
			</div>
			<?php if (!$this->state->get('field.id')):?>
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_align"><?php echo JText::_('COM_TZ_MEDIA_ALIGN') ?></label>
				</div>
				<div class="controls">
					<select size="1" id="f_align">
						<option value="" selected="selected"><?php echo JText::_('COM_TZ_MEDIA_NOT_SET') ?></option>
						<option value="left"><?php echo JText::_('JGLOBAL_LEFT') ?></option>
						<option value="right"><?php echo JText::_('JGLOBAL_RIGHT') ?></option>
					</select>
					<p class="help-block"><?php echo JText::_('COM_TZ_MEDIA_ALIGN_DESC');?></p>
				</div>
			</div>
			<?php endif;?>
		</div>
		<?php if (!$this->state->get('field.id')):?>
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_alt"><?php echo JText::_('COM_TZ_MEDIA_IMAGE_DESCRIPTION') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_alt" value="" />
				</div>
			</div>
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_title"><?php echo JText::_('COM_TZ_MEDIA_TITLE') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_title" value="" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span12 control-group">
				<div class="control-label">
					<label for="f_caption"><?php echo JText::_('COM_TZ_MEDIA_CAPTION') ?></label>
				</div>
				<div class="controls">
					<select size="1" id="f_caption" >
						<option value="" selected="selected" ><?php echo JText::_('JNO') ?></option>
						<option value="1"><?php echo JText::_('JYES') ?></option>
					</select>
					<p class="help-block"><?php echo JText::_('COM_TZ_MEDIA_CAPTION_DESC');?></p>
				</div>
			</div>
		</div>
		<?php endif;?>

		<input type="hidden" id="dirPath" name="dirPath" />
		<input type="hidden" id="f_file" name="f_file" />
		<input type="hidden" id="tmpl" name="component" />

	</div>
</form>

<?php if ($user->authorise('core.create', 'com_tz_media')): ?>
	<form action="<?php echo JURI::base(); ?>index.php?option=com_tz_media&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>&amp;format=<?php echo $this->config->get('enable_flash') == '1' ? 'json' : '' ?>&amp;view=images" id="uploadForm" class="form-horizontal" name="uploadForm" method="post" enctype="multipart/form-data">
		<div id="uploadform" class="well">
			<fieldset id="upload-noflash" class="actions">
                <div class="fileupload-buttonbar">
                    <div class="span12">
                        <input type="file" value="" name="files[]" multiple>
                        <button type="submit" class="btn btn-primary start">
                            <i class="icon-upload icon-white"></i>
                            <span><?php echo JText::_('COM_TZ_MEDIA_START_UPLOAD');?></span>
                        </button>
                        <button type="reset" class="btn btn-warning cancel">
                            <i class="icon-ban-circle icon-white"></i>
                            <span><?php echo JText::_('COM_TZ_MEDIA_CANCEL_UPLOAD');?></span>
                        </button>
                        <p class="help-block"><?php echo $this->config->get('upload_maxsize') == '0' ? JText::_('COM_TZ_MEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_TZ_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></p>
                    </div>

                    <!-- The global progress information -->
                    <div class="span6 fileupload-progress fade">
                        <!-- The global progress bar -->
                        <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                            <div class="bar" style="width:0%;"></div>
                        </div>
                        <!-- The extended global progress information -->
                        <div class="progress-extended">&nbsp;</div>
                    </div>
                </div>

                <!-- The loading indicator is shown during file processing -->
                <div class="fileupload-loading"></div>
                <br>
                <!-- The table listing the files available for upload/download -->
                <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>

            </fieldset>
			<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_tz_media&view=images&tmpl=component&fieldid=' . $input->getCmd('fieldid', '') . '&e_name=' . $input->getCmd('e_name') . '&asset=' . $input->getCmd('asset') . '&author=' . $input->getCmd('author')); ?>" />
		</div>
	</form>
<?php endif; ?>
