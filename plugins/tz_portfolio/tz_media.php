<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

//jimport('joomla.filesystem.file');

/**
 * Pagenavigation plugin class.
 */
class plgTZ_PortfolioTZ_Media extends JPlugin
{

   protected static $modules = array();
	protected static $mods = array();
	/**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onTZPluginPrepare($context, &$article, &$params,&$pluginParams, $page = 0)
	{
        if($context == 'com_tz_portfolio.p_article'){
            if($this -> params -> get('display_position',2) == 1){
                echo $this -> createHtml(&$article, &$params,&$pluginParams);
            }
        }
	}

    public function onTZPluginAfterTitle($context, &$article, &$params,&$pluginParams, $page = 0)
	{
        //Do something
        if($context == 'com_tz_portfolio.p_article'){
            if($this -> params -> get('display_position',2) == 2){
                echo $this -> createHtml(&$article, &$params,&$pluginParams);
            }
        }
    }
    public function onTZPluginBeforeDisplay($context, &$article, &$params,&$pluginParams, $page = 0)
	{
        //Do something
        if($context == 'com_tz_portfolio.p_article'){
            if($this -> params -> get('display_position',2) == 3){
                echo $this -> createHtml(&$article, &$params,&$pluginParams);
            }
        }
    }
    public function onTZPluginAfterDisplay($context, &$article, &$params,&$pluginParams, $page = 0)
	{
        //Do something
        if($context == 'com_tz_portfolio.p_article'){
            if($this -> params -> get('display_position',2) == 4){
                echo $this -> createHtml(&$article, &$params,&$pluginParams);
            }
        }
    }


    protected function createHtml(&$article, &$params,&$pluginParams){

        //Parameter from the config.xml file for the article
            $plgparams  = $pluginParams -> tz_media;
        if($plgparams -> get('tz_media_file')){

            $doc    = JFactory::getDocument();
            $doc -> addCustomTag('<script type="text/javascript" src="http://www.jplayer.org/js/jquery.jplayer.min.js"></script> ');

            if(strtolower($this -> params -> get('theme')) == 'none'){
                return;
            }
            $doc -> addStyleSheet(JUri::base(true).'/plugins/tz_portfolio/tz_media/skin/'.
                                  $this -> params -> get('theme').'/jplayer.'.$this -> params -> get('theme').'.css');

            if($plgparams -> get('tz_media_file') && $plgparams -> get('tz_media_file') != -1){

                //Get filetype
                $_mediaType  = strtolower(JFile::getExt($plgparams -> get('tz_media_file')));

                if($plgparams -> get('tz_media_type','audio') == 'audio'){
                    $mediaType  = $_mediaType;
                    if(!in_array($_mediaType,array('mp3','wma'))){
                        $mediaType  = 'mp3';
                    }
                    $supplied    = 'mp3,wma';
                }
                if($plgparams -> get('tz_media_type','audio') == 'video'){
                    $mediaType  = $supplied   = 'm4v';
                }
            }

            if($plgparams -> get('tz_media_type') && $_mediaType){


                $autoplay   = null;
                if($this -> params -> get('tz_media_auto_play')){
                    $autoplay   = '.jPlayer("play")';// Attempts to Auto-Play the media
                }
                $script[]   = '<script type="text/javascript">';
                $script[]   = ' jQuery(document).ready(function(){';
                $script[]   = '     jQuery("#tzjplayer").jPlayer( {';
                $script[]   = '         ready: function () {';
                $script[]   = '             jQuery(this).jPlayer("setMedia", {';
                $script[]   = '                  '.$mediaType.': "'.JURI::root().$plgparams -> get('tz_media_file').'"';
                $script[]   = '             })'.$autoplay.';';
                $script[]   = '         },';

                //Default volume
                if($defaultVol = $this -> params -> get('tz_media_default_volume','80%')){
                    if(strrpos('%',$defaultVol)){
                        $defaultVol = (int) str_replace('%','',$defaultVol);
                    }
                    $defaultVol /= 100;
                }

                //Show fullscreen button
                if(!$this -> params -> get('tz_media_show_fullscreen',1) ||
                                      ($this -> params -> get('tz_media_show_fullscreen',1) &&
                                     $plgparams -> get('tz_media_type','audio') == 'audio')){
                    $script[]   = 'noFullScreen: {
                                        msie: /msie [0-6]/,
                                        ipad: /ipad.*?os [0-4]/,
                                        iphone: /iphone/,
                                        ipod: /ipod/,
                                        android_pad: /android [0-3](?!.*?mobile)/,
                                        android_phone: /android.*?mobile/,
                                        blackberry: /blackberry/,
                                        windows_ce: /windows ce/,
                                        webos: /webos/,
                                        widows: /windows/
                                   },';
                }

                //Size media
                $script[]   = 'size:{width: "100%"},';

                $script[]   = '         volume : '.$defaultVol.',';
                $script[]   = '         wmode: "'.$this -> params -> get('tz_media_wmode','opaque').'",';
                $script[]   = '         loop: '.$this -> params -> get('tz_media_loop',0).',';

                //Muted default
                if($muted = $this -> params -> get('tz_media_default_muted',0)){
                    $script[]   = 'muted: '.$muted.',';
                }

                //Show volume
                if($this -> params -> get('tz_media_show_volume',1)){
                    $script[]   = '            noVolume:{},';
                }
                else{
                    $script[]   = '            noVolume:{';
                    $script[]   = '                 ipad: /ipad/,
                                                    iphone: /iphone/,
                                                    ipod: /ipod/,
                                                    android_pad: /android(?!.*?mobile)/,
                                                    android_phone: /android.*?mobile/,
                                                    blackberry: /blackberry/,
                                                    windows_ce: /windows ce/,
                                                    webos: /webos/,
                                                    playbook: /playbook/,
                                                    windows:/windows/';
                    $script[]   = '            },';
                }

                $script[]   = '         supplied: "'.$supplied.'",preload: "none",';
                $script[]   = '         swfPath: "'.JUri::base(true).'/plugins/tz_portfolio/tz_media/js"';
                $script[]   = '     })';
                $script[]   = ' })';
                $script[]   = '</script> ';

                $doc -> addCustomTag(implode('',$script));
            }


            $html   = '<div id="jp_container_1" class="jp-video">
                    <div class="jp-type-single">
                        <div id="tzjplayer" class="jp-jplayer"></div>
                        <div class="jp-gui">
                            <div class="jp-video-play">
                                <a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
                            </div>
                            <div class="jp-interface">
                                <div class="jp-progress">
                                    <div class="jp-seek-bar">
                                        <div class="jp-play-bar"></div>
                                    </div>
                                </div>
                                <div class="jp-current-time"></div>
                                <div class="jp-duration"></div>
                                <div class="jp-controls-holder">
                                    <ul class="jp-controls">
                                        <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
                                        <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
                                        <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
                                        <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
                                        <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
                                        <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
                                    </ul>
                                    <div class="jp-volume-bar">
                                        <div class="jp-volume-bar-value"></div>
                                    </div>
                                    <ul class="jp-toggles">
                                        <li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>
                                        <li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>
                                        <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
                                        <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
                                    </ul>
                                </div>
                                <div class="jp-title">
                                    <ul>
                                        <li>'.$article -> title.'</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="jp-no-solution">
                            <span>Update Required</span>
                            To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                        </div>
                    </div>
                </div>';
            return $html;
        }
        return false;
    }
}