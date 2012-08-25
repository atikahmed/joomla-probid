<?php
/**
 * @version     1.0.0
 * @package     com_ptslideshow
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'slideshow.cancel' || document.formvalidator.isValid(document.id('slideshow-form'))) {
			Joomla.submitform(task, document.getElementById('slideshow-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_ptslideshow&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="slideshow-form" class="form-validate" enctype="multipart/form-data">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PTSLIDESHOW_LEGEND_SLIDESHOW'); ?></legend>
			<ul class="adminformlist">
           
			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>
			
			<li><?php echo $this->form->getLabel('title_'); ?>
			<?php echo $this->form->getInput('title_'); ?></li>
						
			<li>
				<?php echo $this->form->getLabel('category_'); ?>
				<?php echo $this->form->getInput('category_'); ?>
			</li>

            <li><?php echo $this->form->getLabel('url_'); ?>
				<?php echo $this->form->getInput('url_'); ?>
			</li>

			<li><?php echo $this->form->getLabel('link'); ?>
			<?php echo $this->form->getInput('link'); ?></li>
            
			<li><?php echo $this->form->getLabel('state'); ?>
                    <?php echo $this->form->getInput('state'); ?></li><li><?php echo $this->form->getLabel('checked_out'); ?>
                    <?php echo $this->form->getInput('checked_out'); ?></li><li><?php echo $this->form->getLabel('checked_out_time'); ?>
                    <?php echo $this->form->getInput('checked_out_time'); ?></li>

            </ul>
			<div class="clr"></div>
			<?php echo $this->form->getLabel('description_'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('description_'); ?>
		</fieldset>
	</div>


	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>