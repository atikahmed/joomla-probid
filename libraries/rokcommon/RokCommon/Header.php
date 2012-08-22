<?php
/**
 * @version   $Id: Header.php 53534 2012-06-06 18:21:34Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKCOMMON') or die;

class RokCommon_Header
{

	/**
	 * @var RokCommon_Header
	 */
	protected $platform_instance;

	/**
	 * @param \RokCommon_Header_Interface $platform_instance
	 */
	public function __construct(RokCommon_Header_Interface $platform_instance)
	{
		$this->platform_instance = $platform_instance;
	}

	/**
	 * @param $file
	 */
	public static function addScript($file)
	{

		$container = RokCommon_Service::getContainer();
		/** @var $self RokCommon_Header_Interface */
		$self = $container->header;
		$self->addScript($file);
	}

	/**
	 * @param $text
	 */
	public static function addInlineScript($text)
	{
		$container = RokCommon_Service::getContainer();
		/** @var $self RokCommon_Header_Interface */
		$self = $container->header;
		$self->addInlineScript($text);
	}

	/**
	 * @param $file
	 */
	public static function addStyle($file)
	{
		$container = RokCommon_Service::getContainer();
		/** @var $self RokCommon_Header_Interface */
		$self = $container->header;
		$self->addStyle($file);
	}

	/**
	 * @param $text
	 */
	public static function addInlineStyle($text)
	{
		$container = RokCommon_Service::getContainer();
		/** @var $self RokCommon_Header_Interface */
		$self = $container->header;
		$self->addInlineStyle($text);
	}
}
