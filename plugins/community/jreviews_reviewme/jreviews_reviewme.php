<?php
/**
 * jReviews - Reviews Extension
 * Copyright (C) 2006-2008 ClickFWD LLC
 * This is not free software, do not distribute it.
 * For licencing information visit http://www.reviewsforjoomla.com
 * or contact sales@reviewsforjoomla.com
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');

class plgCommunityJreviews_reviewme extends CApplications
{

    var $name         = "Reviews of Me";
    var $_name        = 'reviewme';
    var $_path        = '';
    var $_user        = '';
    var $_my        = '';

    function plgCommunityJreviews_reviewme(& $subject, $config)
    {
        $this->_path    = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_jreviews';
        $this->_user    = CFactory::getActiveProfile();
        $this->_my        = CFactory::getUser();
            
        parent::__construct($subject, $config);
    }

    function onProfileDisplay()
    {
        if( !file_exists( $this->_path . DS . 'admin.jreviews.php' ) ){
            return JText::_('jReviews is not installed. Please contact site administrator.');
        }else{
            $user        = CFactory::getActiveProfile();
            $userId = $user->id;
            
            $cacheSetting = $this->params->get('cache', 1) ? JApplication::getCfg('caching') : 0;
            
            # Load CSS stylesheets -- done here because when cache is on css is not loaded
            if($cacheSetting) {
                # MVC initalization script
                if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);    
                require('components' . DS . 'com_jreviews' . DS . 'jreviews' . DS . 'framework.php');
    
                //Create config file
                $eParams['data']['controller'] = 'common';
                $eParams['data']['action'] = 'index';
                $Dispatcher = new S2Dispatcher('jreviews',false, true);
                $Dispatcher->dispatch($eParams);                    
                unset($Dispatcher);

                $Access = Configure::read('JreviewsSystem.Access');
                $Config = Configure::read('JreviewsSystem.Config');

                App::import('Helper','html');
                $Html = ClassRegistry::getClass('HtmlHelper');
                $Html->viewTheme = $Config->template;
                $Html->app = 'jreviews';        
                App::import('Helper','libraries','jreviews');
                $Libraries = ClassRegistry::getClass('LibrariesHelper');
                $Libraries->Config = $Config;
                
                $assets = array(
                    'css'=>array('theme','theme.detail','theme.form','jq.ui.core','paginator'),
                    'js'=>array('jreviews','jquery'=>'jquery','jq.ui.core','jq.jreviews.plugins')
                );
                                
                if($Access->canAddReview() || $Access->isEditor()) 
                {                 
                    $assets['js'][] = 'jreviews.fields';
                    
                    if($Config->rating_selector == 'stars'){
                        $assets['js'][] = 'jq.ui.rating';
                    }           
                    $assets['js'][] = 'jq.tooltip';
                }      

                $Html->css(arrayFilter($assets['css'], $Libraries->css()));      
                $Html->js(arrayFilter($assets['js'], $Libraries->js()));

               ?>
                <script type="text/javascript">
                /* <![CDATA[ */
                jQuery(document).ready(function() 
                {
                    <?php if($Access->canAddReview && !$Access->moderateReview() && $Config->facebook_enable && $Config->facebook_reviews):?>
                    if(!jQuery('#fb-root').length) jQuery("body").append('<div id="fb-root"></div>');
                    jreviews.facebook.init({
                        'appid':'<?php echo $Config->facebook_appid;?>',
                        'optout':<?php echo $Config->facebook_optout;?>,
                        'success':function(){
                            jreviews.facebook.checkPermissions({
                                'onPermission':function(){jreviews.facebook.setCheckbox('jr_submitButton',true);},
                                'onNoSession':function(){jreviews.facebook.setCheckbox('jr_submitButton',false);}
                            });
                        },
                        'publish_text': '<?php __t("Publish to Facebook",false,true);?>'
                    });
                    <?php endif;?>
                });
                /* ]]> */
                </script>    
                <?php                 
            }

            $cache =& JFactory::getCache('plgCommunityJreviews_reviewme');
            $cache->setCaching($cacheSetting);
            $callback = array('plgCommunityJreviews_reviewme', '_getPage');
            $contents = $cache->call($callback, $userId, $this->params, $cacheSetting);
            return $contents;                                        
        }    
    }
    
    function _getPage($userId, $params, $cacheSetting)
    {
        if(!$cacheSetting) {
                # MVC initalization script
                if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);    
                require('components' . DS . 'com_jreviews' . DS . 'jreviews' . DS . 'framework.php');            
        }

        Configure::write('Libraries.disableJS',array('jquery'));
        
        # Populate $params array with module settings        
        $eParams['data']['extension'] = $params->get('integration','com_community_access'); // Access | Field
        $eParams['data']['tmpl_suffix'] = '';
        $eParams['data']['controller'] = 'everywhere';
        $eParams['data']['action'] = 'index';
        $eParams['data']['listing_id'] = $userId;
        $eParams['data']['limit_special'] = $params->get( 'list_limit', 10 );                

        // Load dispatch class
        $Dispatcher = new S2Dispatcher('jreviews',true);
        
        $eDetail = $Dispatcher->dispatch($eParams);

        if($eDetail) {
        
            $form = $eDetail['output'];
                    
            return $form;
        }    

    }
    
}
