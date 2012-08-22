<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>

<table width="100%">
<tr>
	<td width="50%" valign="top">

<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="adminlist">
			<tr>
				<td>
					<div id="cpanel">
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_BACK_TO_RSTICKETSPRO'); ?>">
							<a href="index.php?option=com_rsticketspro">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/back.png', JText::_('RST_BACK_TO_RSTICKETSPRO')); ?>
								<span><?php echo JText::_('RST_BACK_TO_RSTICKETSPRO'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_KB_CATEGORIES_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=kbcategories">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/kbcategories.png', JText::_('RST_KB_CATEGORIES')); ?>
								<span><?php echo JText::_('RST_KB_CATEGORIES'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_KB_ARTICLES_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=kbcontent">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/kbarticles.png', JText::_('RST_KB_ARTICLES')); ?>
								<span><?php echo JText::_('RST_KB_ARTICLES'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_KB_CONVERSION_RULES_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=kbrules">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/kbrules.png', JText::_('RST_KB_CONVERSION_RULES')); ?>
								<span><?php echo JText::_('RST_KB_CONVERSION_RULES'); ?></span>
							</a>
						</div>
					</div>
					<div style="float: left">
						<div class="icon hasTip" title="<?php echo JText::_('RST_KB_TEMPLATE_DESC'); ?>">
							<a href="index.php?option=com_rsticketspro&amp;view=kbtemplate">
								<?php echo JHTML::_('image', 'administrator/components/com_rsticketspro/assets/images/kbtemplate.png', JText::_('RST_KB_TEMPLATE')); ?>
								<span><?php echo JText::_('RST_KB_TEMPLATE'); ?></span>
							</a>
						</div>
					</div>
					</div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>

<span class="rsticketspro_clear"></span>