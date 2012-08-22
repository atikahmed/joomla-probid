<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<h2><?php echo JText::_('RSM_VIEWING_TRANSACTION_LOG'); ?></h2>
<div>
	<?php echo $this->log ? nl2br($this->escape($this->log)) : JText::_('RSM_LOG_IS_EMPTY'); ?>
</div>