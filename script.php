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

define('COM_TZ_MEDIA_FILE_BASE','media'.DIRECTORY_SEPARATOR.'tz_media');
jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');

class com_tz_mediaInstallerScript{
    function postflight($type, $parent){
        //Create folder
        $mediaFolderPath    = JPATH_SITE.DIRECTORY_SEPARATOR.COM_TZ_MEDIA_FILE_BASE;

        if(!JFolder::exists($mediaFolderPath)){
            JFolder::create($mediaFolderPath);
        }
        if(!JFile::exists($mediaFolderPath.DIRECTORY_SEPARATOR.'index.html')){
            JFile::write($mediaFolderPath.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
        }
        
        JFactory::getLanguage()->load('com_tz_media');
        $manifest   = $parent -> get('manifest');
        $params     = new JRegistry();

        $query  = 'SELECT params FROM #__extensions'
                  .' WHERE `type`="component" AND `name`="'.strtolower($manifest -> name).'"';
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        $db -> query();

        $paramNames = array();

        if($db -> loadResult()){
            $params -> loadString($db ->loadResult());
            if(count($params -> toArray())>0){
                foreach($params -> toArray() as $key => $val){
                    $paramNames[]   = $key;
                }
            }
        }

        $fields     = $manifest -> xPath('config/fields/field');

        foreach($fields as $field){
            $attribute  = $field -> attributes();
            if(!in_array((string)$attribute -> name,$paramNames)){
                if($attribute -> multiple == 'true'){
                    $arr   = array();
                    $options    = $manifest -> xPath('config/fields/field/option');
                    foreach($options as $option){
                        $opAttr = $option -> attributes();
                        $arr[]  = (string)$opAttr -> value;
                    }

                    $params -> set((string) $attribute -> name,$arr);
                }
                else
                    $params -> set((string)$attribute -> name,(string)$attribute ->default);
            }
        }

        $params = $params -> toString();

        $query  = 'UPDATE #__extensions SET `params`=\''.$params.'\''
                  .' WHERE `name`="'.strtolower($manifest -> name).'"'
                  .' AND `type`="component"';

        $db -> setQuery($query);
        $db -> query();

        //Install plugins
        $status = new stdClass;
        $status->modules = array();
        $src = $parent->getParent()->getPath('source');

        if(version_compare( JVERSION, '1.6.0', 'ge' )) {
            $modules = $parent->getParent()->manifest->xpath('modules/module');

            foreach($modules as $module){
                $result = null;
                $mname = $module->attributes() -> module;
                $client = $module->attributes() -> client;
                if(is_null($client)) $client = 'site';
                ($client=='administrator')? $path=$src.'/'.'administrator'.'/'.'modules'.'/'.$mname: $path = $src.'/'.'modules'.'/'.$mname;
                $installer = new JInstaller();
                $result = $installer->install($path);
                $status->modules[] = array('name'=>$mname,'client'=>$client, 'result'=>$result);
            }

            $plugins = $parent->getParent()->manifest->xpath('plugins/plugin');
            foreach($plugins as $plugin){
                $result     = null;
                $folder     = null;
                $pname      = $plugin->attributes() -> plugin;
                $group      = $plugin->attributes() -> group;
                $folder     = $plugin -> attributes() -> folder;
                if(isset($folder)){
                    $folder = $plugin -> attributes() -> folder;
                }
                $path   = $src.'/'.'plugins'.'/'.$group.'/'.$folder;

                $installer = new JInstaller();
                $result = $installer->install($path);
                $status->plugins[] = array('name'=>$pname,'group'=>$group, 'result'=>$result);
                if(isset($plugin -> attributes() -> enabled) && $plugin -> attributes() -> enabled){
                    $query  = 'UPDATE #__extensions SET `enabled`=1'
                              .' WHERE `type`="plugin" AND `element`="'.$pname.'" AND `folder`="'.$group.'"';
                    $this -> updateSql($query);
                }
            }
        }
        $this -> installationResult($status);
    }

    function uninstall($parent){
        $mediaFolderPath    = JPATH_SITE.'/'.COM_TZ_MEDIA_FILE_BASE;
        if(JFolder::exists($mediaFolderPath)){
            JFolder::delete($mediaFolderPath);
        }

        $status = new stdClass();
        $status->modules = array ();
        $status->plugins = array ();

        $modules =  $parent -> getParent() -> manifest -> xpath('modules/module');
        $plugins =  $parent -> getParent() -> manifest -> xpath('plugins/plugin');

        $result = null;
        if($modules){
            foreach($modules as $module){
                $mname = (string)$module->attributes() -> module;
                $client = (string)$module->attributes() -> client;

                $db = JFactory::getDBO();
                $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='module' AND `element` = ".$db->Quote($mname)."";
                $db->setQuery($query);
                $IDs = $db->loadColumn();
                if (count($IDs)) {
                    foreach ($IDs as $id) {
                        $installer = new JInstaller;
                        $result = $installer->uninstall('module', $id);
                    }
                }
                $status->modules[] = array ('name'=>$mname, 'client'=>$client, 'result'=>$result);
            }
        }

        if($plugins){
            foreach ($plugins as $plugin) {

                $pname = (string)$plugin->attributes() -> plugin;
                $pgroup = (string)$plugin->attributes() -> group;

                $db = JFactory::getDBO();
                $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND `element` = "
                         .$db->Quote($pname)." AND `folder` = ".$db->Quote($pgroup);
                $db->setQuery($query);
                $IDs = $db->loadColumn();
                if (count($IDs)) {
                    foreach ($IDs as $id) {
                        $installer = new JInstaller;
                        $result = $installer->uninstall('plugin', $id);
                    }
                }
                $status->plugins[] = array ('name'=>$pname, 'group'=>$pgroup, 'result'=>$result);
            }
        }
        $this -> uninstallationResult($status);
    }

    protected function updateSql($query){
        $db = JFactory::getDbo();
        $db -> setQuery($query);
        $db -> query();
    }

    public function installationResult($status){
        JFactory::getLanguage() -> load('com_tz_media');
        $rows   = 0;
?>
        <h2><?php echo JText::_('COM_TZ_MEDIA_HEADING_INSTALL_STATUS'); ?></h2>
        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th class="title" colspan="2"><?php echo JText::_('COM_TZ_MEDIA_EXTENSION'); ?></th>
                    <th width="30%"><?php echo JText::_('COM_TZ_MEDIA_STATUS'); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            <tbody>
                <tr class="row0">
                    <td class="key" colspan="2"><?php echo JText::_('COM_TZ_MEDIA').' '.JText::_('COM_TZ_MEDIA_COMPONENT'); ?></td>
                    <td><strong><?php echo JText::_('COM_TZ_MEDIA_INSTALLED'); ?></strong></td>
                </tr>
                <?php if (count($status->modules)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_MEDIA_MODULE'); ?></th>
                    <th><?php echo JText::_('COM_TZ_MEDIA_CLIENT'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module): ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo $module['name']; ?></td>
                    <td class="key"><?php echo ucfirst($module['client']); ?></td>
                    <td><strong><?php echo ($module['result'])?JText::_('COM_TZ_MEDIA_INSTALLED'):JText::_('COM_TZ_MEDIA_NOT_INSTALLED'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (count($status->plugins)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_MEDIA_PLUGIN'); ?></th>
                    <th><?php echo JText::_('COM_TZ_MEDIA_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin): ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                    <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                    <td><strong><?php echo ($plugin['result'])?JText::_('COM_TZ_MEDIA_INSTALLED'):JText::_('COM_TZ_MEDIA_NOT_INSTALLED'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (isset($status -> languages) AND count($status->languages)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_MEDIA_LANGUAGES'); ?></th>
                    <th><?php echo JText::_('COM_TZ_MEDIA_COUNTRY'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->languages as $language): ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo ucfirst($language['language']); ?></td>
                    <td class="key"><?php echo ucfirst($language['country']); ?></td>
                    <td><strong><?php echo ($language['result'])?JText::_('COM_TZ_MEDIA_INSTALLED'):JText::_('COM_TZ_MEDIA_NOT_INSTALLED'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
<?php
    }
    function uninstallationResult($status){
        JFactory::getLanguage()->load('com_tz_media');
        $rows   = 0;
?>
        <h2><?php echo JText::_('COM_TZ_MEDIA_HEADING_REMOVE_STATUS'); ?></h2>
        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th class="title" colspan="2"><?php echo JText::_('COM_TZ_MEDIA_EXTENSION'); ?></th>
                    <th width="30%"><?php echo JText::_('COM_TZ_MEDIA_STATUS'); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            <tbody>
                <tr class="row0">
                    <td class="key" colspan="2"><?php echo JText::_('COM_TZ_MEDIA').' '.JText::_('COM_TZ_MEDIA_COMPONENT'); ?></td>
                    <td><strong><?php echo JText::_('COM_TZ_MEDIA_REMOVED'); ?></strong></td>
                </tr>
                <?php if (count($status->modules)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_MEDIA_MODULE'); ?></th>
                    <th><?php echo JText::_('COM_TZ_MEDIA_CLIENT'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module): ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo $module['name']; ?></td>
                    <td class="key"><?php echo ucfirst($module['client']); ?></td>
                    <td><strong><?php echo ($module['result'])?JText::_('COM_TZ_MEDIA_REMOVED'):JText::_('COM_TZ_MEDIA_NOT_REMOVED'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (count($status->plugins)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_MEDIA_PLUGIN'); ?></th>
                    <th><?php echo JText::_('COM_TZ_MEDIA_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin): ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                    <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                    <td><strong><?php echo ($plugin['result'])?JText::_('COM_TZ_MEDIA_REMOVED'):JText::_('COM_TZ_MEDIA_NOT_REMOVED'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (isset($status -> languages) && count($status->languages)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_MEDIA_LANGUAGES'); ?></th>
                    <th><?php echo JText::_('COM_TZ_MEDIA_COUNTRY'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->languages as $language): ?>
                <tr class="row<?php echo (++ $rows % 2); ?>">
                    <td class="key"><?php echo ucfirst($language['language']); ?></td>
                    <td class="key"><?php echo ucfirst($language['country']); ?></td>
                    <td><strong><?php echo ($language['result'])?JText::_('COM_TZ_MEDIA_REMOVED'):JText::_('COM_TZ_MEDIA_NOT_REMOVED'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
<?php
    }
}
?>