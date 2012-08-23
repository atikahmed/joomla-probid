<?php
/**
 * @version		1.5 October 6, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * @package     gantry
 * @subpackage  admin.elements
 */
class JFormFieldJomSocialEventCategory extends JFormFieldList
{
    protected $type = 'JomSocialEventCategory';
    	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

        require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
        CFactory::load( 'helpers' , 'event' );
        $model		= CFactory::getModel( 'events' );

        $categories = $model->getCategories();

		$options = array();
        $options[] = JHTML::_('select.option', 0, '--');
		foreach ($categories as $option)
		{
			$options[] = JHTML::_('select.option', $option->id, $option->name);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
