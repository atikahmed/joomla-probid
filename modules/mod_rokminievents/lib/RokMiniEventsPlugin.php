<?php
/**
 * @version   1.5 October 6, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 */
class RokMiniEventsPlugin extends JPlugin
{

    public static $ROKMINIEVENTS_ROOT;
    public static $SOURCE_DIR;

    public function __construct($parent = null)
    {
        if (!defined('ROKMINIEVENTS')) define('ROKMINIEVENTS', 'ROKMINIEVENTS');

        // Set base dirs
        self::$ROKMINIEVENTS_ROOT = JPATH_ROOT . '/modules/mod_rokminievents';
        self::$SOURCE_DIR = self::$ROKMINIEVENTS_ROOT . '/lib/RokMiniEvents/Source';

        //load up the RTCommon
        require_once(self::$ROKMINIEVENTS_ROOT . '/lib/include.php');

        parent::__construct($parent);
    }

    public function onContentPrepareForm($form, $data)
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin()) return;

        $option = JRequest::getWord('option');
        $layout = JRequest::getWord('layout');
        $task = JRequest::getWord('task');

        $module = $this->getModuleType($data);


        if ($option == 'com_modules' && $layout == 'edit' && $module == 'mod_rokminievents')
        {
            JForm::addFieldPath(JPATH_ROOT . '/modules/mod_rokminievents/fields');

            //Find Sources
            $sources = RokMiniEvents_SourceLoader::getAvailableSources(self::$SOURCE_DIR);

            foreach ($sources as $source_name => $source)
            {
                if (file_exists($source->paramspath) && is_readable($source->paramspath))
                {
                    $form->loadFile($source->paramspath, false);
                    JForm::addFieldPath( dirname($source->paramspath) . "/" . $source->name );
                    //$this->element_dirs[] = dirname($source->paramspath) . "/" . $source->name;
                    $language =& JFactory::getLanguage();
                    $language->load('com_'.$source->name, JPATH_ADMINISTRATOR);
                    $language->load($source->name, dirname($source->paramspath), $language->getTag(), true);
                }
            }

            $subfieldform = RokSubfieldForm::getInstance($form);

            if (!empty($data) && isset($data->params)) $subfieldform->setOriginalParams($data->params);

            if ($task == 'save' || $task == 'apply')
            {
                $subfieldform->makeSubfieldsVisable();
            }
        }
    }

    protected function getModuleType(&$data)
    {
        if (is_array($data) && isset($data['module']))
        {
            return $data['module'];
        }
        elseif (is_array($data) && empty($data))
        {
            $form = JRequest::getVar('jform');
            if (is_array($form) && array_key_exists('module',$form))
            {
                return $form['module'];
            }
        }
        if (is_object($data) && method_exists( $data , 'get'))
        {
            return $data->get('module');
        }
        return '';
    }
}

