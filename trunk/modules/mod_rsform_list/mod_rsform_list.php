<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Check if the helper exists
jimport('joomla.filesystem.file');
$helper = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'rsform.php';
if (!JFile::exists($helper))
	return;

// Load Helper functions
require_once($helper);
require_once(dirname(__FILE__).DS.'helper.php');

// Objects
$user =& JFactory::getUser();
$db	  =& JFactory::getDBO();

// Params
$formId			 = (int) $params->def('formId', 1);
$moduleclass_sfx = $params->def('moduleclass_sfx', '');
$userId 		 = $params->def('userId', 0);

// Template params
$template_module      = $params->def('template_module', '');
$template_formdatarow = $params->def('template_formdatarow', '');
$template_formdetail  = $params->def('template_formdetail', '');

$helper = new ModRSFormListHelper($params);
$requested_details = JRequest::getInt('detail'.$formId);

if (!$requested_details)
{
	$submissions = $helper->getSubmissions();
	$pagination  = $helper->getPagination();
	$headers	 = $helper->getHeaders();
	$form		 = $helper->getForm();
	
	$formdata = '';
	$i  	  = 0;
	$uri 	  = JFactory::getURI();
	$uri->delVar('detail'.$formId);
	$url = $uri->toString();
	if (strpos($url, '?') !== false)
		$url .= '&';
	else
		$url .= '?';
	
	foreach ($submissions as $SubmissionId => $submission)
	{
		list($replace, $with) = $helper->getReplacements($submission['UserId']);
		$replace = array_merge($replace, array('{global:date_added}', '{global:submission_id}', '{global:counter}', '{details}','{global:confirmed}'));
		$with 	 = array_merge($with, array($submission['DateSubmitted'], $SubmissionId, $pagination->getRowOffset($i), '<a href="'.$url.'detail'.$formId.'='.$SubmissionId.'">',$submission['confirmed']));
		
		foreach ($headers as $header)
		{
			if (!isset($submission['SubmissionValues'][$header]['Value']))
				$submission['SubmissionValues'][$header]['Value'] = '';
				
			$replace[] = '{'.$header.':value}';
			$with[] = $submission['SubmissionValues'][$header]['Value'];
			
			if (!empty($submission['SubmissionValues'][$header]['Path']))
			{
				$replace[] = '{'.$header.':path}';
				$with[] = $submission['SubmissionValues'][$header]['Path'];
			}
		}
		
		$formdata .= str_replace($replace, $with, $template_formdatarow);
		
		$i++;
	}

	$html  = str_replace('{formdata}', $formdata, $template_module);
	$html .= '<div>'.$pagination->getResultsCounter().'</div>';
	$html .= '<div>'.$pagination->getPagesLinks().'</div>';
}
else
{
	$detail = JRequest::getInt('detail'.$formId);
	if ($userId != 'login' && $userId != 0)
	{
		$userId = explode(',', $userId);
		JArrayHelper::toInteger($userId);
	}
	$db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$detail."'");
	$submission = $db->loadObject();
	if (!$submission || ($submission->FormId != $formId) || ($userId == 'login' && $submission->UserId != $user->get('id')) || (is_array($userId) && !in_array($user->get('id'), $userId)))
	{
		JError::raiseWarning(500, JText::_('ALERTNOTAUTH'));
		return;
	}
	
	$confirmed = $submission->confirmed ? JText::_('RSFP_YES') : JText::_('RSFP_NO');
	list($replace, $with) = RSFormProHelper::getReplacements($detail, true);
	list($replace2, $with2) = $helper->getReplacements($submission->UserId);
	$replace = array_merge($replace, $replace2, array('{global:submission_id}', '{global:date_added}','{global:confirmed}'));
	$with 	 = array_merge($with, $with2, array($detail, $submission->DateSubmitted,$confirmed));
	
	$html = str_replace($replace, $with, $template_formdetail);
}

// Display template
require(JModuleHelper::getLayoutPath('mod_rsform_list'));