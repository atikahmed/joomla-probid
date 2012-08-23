<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>

<?php 
	$user = JFactory::getUser();
	$type = (!$user->get('guest')) ? 'logout' : 'login';
?>

<div class="profooterlogo">logo</div>

<?php if ($type == 'login'): ?>
<div class="prologin">
	<div class="proaccount">
		<a href="#">Create a Free Account</a>
	</div>
</div>
<?php endif; ?>

<?php if ($type == 'logout'): ?>
	<div class="proupaccount">
		<a href="#">Upgrade Account Now</a>
	</div>
<?php endif; ?>

<?php if ($params->get('posttext')): ?>
	<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
	</div>
<?php endif; ?>