<?php
/**
 * @version   $Id: Joomla.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKCOMMON') or die;

/**
 *
 */
class RokCommon_Header_Joomla implements RokCommon_Header_Interface
{
	/**
	 * @var \JDocument
	 */
	protected $document;

	/**
	 *
	 */
	public function __construct()
	{
		$this->document = JFactory::getDocument();
	}

	/**
	 * @param       $file
	 *
	 * @internal param int $priority
	 * @internal param array $browserspecific
	 */
	public function addScript($file)
	{
		$path_parts = pathinfo($file);

		$this->document->addScript($file);
	}

	/**
	 * @param $text
	 */
	public function addInlineScript($text)
	{
		$this->document->addScriptDeclaration($text);
	}

	/**
	 * @param       $file
	 *
	 * @internal param int $priority
	 * @internal param array $browserspecific
	 */
	public function addStyle($file)
	{
		$this->document->addStyleSheet($file);
	}

	/**
	 * @param $text
	 */
	public function addInlineStyle($text)
	{
		$this->document->addStyleDeclaration($text);
	}

}
