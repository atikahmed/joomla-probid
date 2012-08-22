<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

?>

</td>
<td width="50%" valign="top" align="center">

<form action="index.php?option=com_rsticketspro" method="post" name="adminForm" id="adminForm">
<table border="1" width="100%" class="thisform">
	<tr class="thisform">
		<th class="cpanel" colspan="2"><?php echo _RSTICKETSPRO_PRODUCT . ' ' . _RSTICKETSPRO_VERSION_LONG. ' rev ' . _RSTICKETSPRO_VERSION; ?></th></td>
	 </tr>
	 <tr class="thisform"><td bgcolor="#FFFFFF" colspan="2"><br />
  <div style="width=100%" align="center">
  <img src="../administrator/components/com_rsticketspro/assets/images/rsticketspro.jpg" align="middle" alt="RSTickets! Pro logo"/>
  <br /><br /></div>
  </td></tr>
	 <tr class="thisform">
		<td width="120" bgcolor="#FFFFFF"><?php echo JText::_('RST_INSTALLED_VERSION'); ?></td>
		<td bgcolor="#FFFFFF"><?php echo _RSTICKETSPRO_VERSION_LONG; ?></td>
	 </tr>
	 <tr class="thisform">
		<td bgcolor="#FFFFFF"><?php echo JText::_('RST_COPYRIGHT'); ?></td>
		<td bgcolor="#FFFFFF"><?php echo _RSTICKETSPRO_COPYRIGHT;?></td>
	 </tr>
	 <tr class="thisform">
		<td bgcolor="#FFFFFF"><?php echo JText::_('RST_LICENSE'); ?></td>
		<td bgcolor="#FFFFFF"><?php echo _RSTICKETSPRO_LICENSE;?></td>
	 </tr>
	 <tr class="thisform">
		<td valign="top" bgcolor="#FFFFFF"><?php echo JText::_('RST_AUTHOR'); ?></td>
		<td bgcolor="#FFFFFF"><?php echo _RSTICKETSPRO_AUTHOR;?></td>
	 </tr>
	 <tr class="<?php echo (!$this->code) ? 'thisformError' : 'thisformOk'; ?>">
		<td valign="top"><?php echo JText::_('RST_YOUR_CODE'); ?></td>
		<td>
			<?php echo (!$this->code) ? '<input type="text" name="global_register_code" value="" />': $this->code; ?>
		</td>
	 </tr>
	 <tr class="<?php echo (!$this->code) ? 'thisformError' : 'thisformOk'; ?>">
		<td valign="top">&nbsp;</td>
		<td>
			<?php if (!$this->code) { ?>
			<input type="submit" name="register" value="<?php echo JText::_('RST_UPDATE_REGISTRATION');?>" /><br/>
			<?php } else { ?>
			<input type="button" name="register" value="<?php echo JText::_('RST_MODIFY_REGISTRATION');?>" onclick="javascript:submitbutton('saveRegistration');"/>
			<?php }	?>
			
		</td>
	 </tr>
  </table>
  <p align="center"><a href="http://www.rsjoomla.com/joomla-components/joomla-security.html" target="_blank"><img src="components/com_rsticketspro/assets/images/rsfirewall-approved.gif" align="middle" alt="RSFirewall! Approved"/></a></p>
<input type="hidden" name="filetype" value="rsticketsproupdate"/>
<input type="hidden" name="task" value="saveRegistration"/>
<input type="hidden" name="option" value="com_rsticketspro"/>
</form>

</td>
</tr>
</table>