<?php
/**
* @version 2.0.0
* @package RSTickets! Pro 2.0.0
* @copyright (C) 2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>

<script type="text/javascript">
<!--
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel')
	{
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (form.name.value.length == 0)
		return alert('<?php echo JText::_('RST_KB_RULE_NAME_ERROR', true); ?>');
	else
	{
		var conditionsContainer = document.getElementById('rst_conditions');
		var conditions = conditionsContainer.childNodes;
		if (conditions.length < 1)
			return alert('<?php echo JText::_('RST_KB_RULE_NO_CONDITION_ERROR', true); ?>');
		
		for (var i=0; i<conditions.length; i++)
		{
			var children = conditions[i].childNodes;
			var selectCustomFieldValue = false;
			for (var j=0; j<children.length; j++)
			{
				if (children[j].name == 'select_type[]')
				{
					var selectType = children[j];
					continue;
				}
				if (children[j].name == 'select_condition[]')
				{
					var selectCondition = children[j];
					continue;
				}
				if (children[j].className == 'responseSpan')
				{
					var spanChildren = children[j].childNodes;
					for (var k=0; k<spanChildren.length; k++)
					{
						if (spanChildren[k].name == 'select_value[]' && spanChildren[k].disabled == false)
						{
							var selectValue = spanChildren[k];
							continue;
						}
					}
				}
				if (children[j].className == 'responseSpan2')
				{
					var spanChildren = children[j].childNodes;
					for (var k=0; k<spanChildren.length; k++)
					{
						if (spanChildren[k].name == 'select_custom_field_value[]')
						{
							var selectCustomFieldValue = spanChildren[k];
							continue;
						}
					}
				}
			}
			
			if (selectType.value == '')
			{
				selectType.style.borderColor = 'red';
				return alert('<?php echo JText::_('RST_KB_RULE_SELECT_TYPE_ERROR', true); ?>');
			}
			else
				selectType.style.borderColor = '';
			
			if (selectType.value == 'custom_field' && selectCustomFieldValue)
			{
				if (selectCustomFieldValue.value == '')
				{
					selectType.style.borderColor = 'red';
					return alert('<?php echo JText::_('RST_KB_RULE_SELECT_CUSTOM_FIELD_ERROR', true); ?>');
				}
				else
					selectType.style.borderColor = '';
			}
				
			if (selectCondition.value == '')
			{
				selectCondition.style.borderColor = 'red';
				return alert('<?php echo JText::_('RST_KB_RULE_SELECT_CONDITION_ERROR', true); ?>');
			}
			else
				selectCondition.style.borderColor = '';
			
			if (selectValue.value == '')
			{
				selectValue.style.borderColor = 'red';
				return alert('<?php echo JText::_('RST_KB_RULE_SELECT_VALUE_ERROR', true); ?>');
			}
			else
				selectValue.style.borderColor = '';
		}
	}
	
	submitform(pressbutton);
}

<?php if (RSTicketsProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>

function rst_change_select_type()
{
	var td = this.parentNode;
	var children = td.childNodes;
	for (var i=0; i<children.length; i++)
	{
		if (children[i].name == 'select_condition[]')
		{
			selectCondition = children[i];
			continue;
		}
		if (children[i].className == 'responseSpan')
		{
			responseSpan = children[i];
			continue;
		}
		if (children[i].className == 'responseSpan2')
		{
			responseSpan2 = children[i];
			continue;
		}
	}
	
	responseSpan.innerHTML = '';
	responseSpan2.innerHTML = '';
	
	selectCondition.options.selectedIndex = 0;
	selectCondition.disabled = true;
	selectCondition.style.display = 'none';
	
	switch (this.value)
	{		
		case 'department':
		case 'priority':
		case 'status':
			selectCondition.disabled = false;
			selectCondition.style.display = '';
			
			var xmlHttp = rst_get_xml_http_object();
			
			if (this.value == 'department')
				task = 'showDepartments';
			else if (this.value == 'priority')
				task = 'showPriorities';
			else if (this.value == 'status')
				task = 'showStatuses';
			
			var url = 'index.php?option=com_rsticketspro&controller=kbrules&task=' + task;

			xmlHttp.onreadystatechange = function() {
				if (xmlHttp.readyState==4)
				{
					var select = document.createElement('select');
					select.name = 'select_value[]';
					select.disabled = true;
					select.style.display = 'none';
					
					try {
						var options = eval(xmlHttp.responseText);
						if (options)
							for (var i=0; i<options.length; i++)
							{
								var option = document.createElement('option');
								option.value = options[i].id;
								option.text = options[i].name;
								select.options.add(option);
							}
					}
					catch (e)
					{
						alert(e);
					}
					
					responseSpan.appendChild(select);
					
					var textbox = document.createElement('input');
					textbox.type = 'text';
					textbox.name = 'select_value[]';
					textbox.disabled = true;
					textbox.style.display = 'none';
					textbox.value = '';
					
					responseSpan.appendChild(textbox);
				}
			};
			
			xmlHttp.open('GET', url, true);
			//xmlHttp.setRequestHeader('Content-type','application/x-www-form-urlencoded; charset=UTF-8');
			//xmlHttp.setRequestHeader("Content-length", 0);
			//xmlHttp.setRequestHeader("Connection", "close");
			xmlHttp.send(null);
		break;
		
		case 'subject':
			selectCondition.disabled = false;
			selectCondition.style.display = '';
			
			var textbox = document.createElement('input');
			textbox.type = 'text';
			textbox.name = 'select_value[]';
			textbox.disabled = true;
			textbox.style.display = 'none';
			textbox.value = '';
			
			responseSpan.appendChild(textbox);
		break
		
		case 'message':
			selectCondition.disabled = false;
			selectCondition.style.display = '';
			
			var textarea = document.createElement('textarea');
			textarea.name = 'select_value[]';
			textarea.disabled = true;
			textarea.style.display = 'none';
			textarea.value = '';
			
			responseSpan.appendChild(textarea);
		break;
		
		case 'custom_field':			
			var xmlHttp = rst_get_xml_http_object();
			
			var url = 'index.php?option=com_rsticketspro&controller=kbrules&task=showCustomFields';

			xmlHttp.onreadystatechange = function() {
				if (xmlHttp.readyState==4)
				{					
					var select = document.createElement('select');
					var option = document.createElement('option');
					option.value = '';
					option.text = '<?php echo JText::_('RST_PLEASE_SELECT', true); ?>';
					select.options.add(option);
					
					select.name = 'select_custom_field_value[]';
					select.onchange = rst_show_custom_field_values;
					
					toEval = xmlHttp.responseText.split("\n");
					try {
						var departments = eval(toEval[0]);
						var options = eval(toEval[1]);
						
						for (var i=0; i<departments.length; i++)
						{
							var group = document.createElement('optgroup');
							group.label = departments[i].name;
							
							for (var j=0; j<options.length; j++)
							{
								if (options[j].department_id != departments[i].id)
									continue;
									
								var option = document.createElement('option');
								option.value = options[j].id;
								if (typeof(option.innerText) != 'undefined')
									option.innerText = options[j].name;
								else
									option.text = options[j].name;
								
								group.appendChild(option);
							}
							
							select.appendChild(group);
						}
					}
					catch (e)
					{
						alert(e);
					}
					
					responseSpan2.appendChild(select);
					
					var textbox = document.createElement('input');
					textbox.type = 'text';
					textbox.name = 'select_value[]';
					textbox.disabled = true;
					textbox.style.display = 'none';
					textbox.value = '';
					
					responseSpan.appendChild(textbox);
				}
			};
			
			xmlHttp.open('GET', url, true);
			xmlHttp.send(null);
		break;
	}
}

function rst_change_select_condition()
{
	var td = this.parentNode;
	var children = td.childNodes;
	var responseSpan = false;
	for (var i=0; i<children.length; i++)
		if (children[i].className == 'responseSpan')
		{
			responseSpan = children[i];
			break;
		}
	
	var children = responseSpan.childNodes;
	
	for (var i=0; i<children.length; i++)
	{
		children[i].disabled = true;
		children[i].style.display = 'none';
	}
	
	if (!children.length)
		return;
	
	switch (this.value)
	{
		case 'neq':
		case 'eq':
			children[0].disabled = false;
			children[0].style.display = '';
		break;
		
		case 'like':
		case 'notlike':
			if (children.length == 2)
			{
				children[1].disabled = false;
				children[1].style.display = '';
			}
			else
			{
				children[0].disabled = false;
				children[0].style.display = '';
			}
		break
	}
}

function rst_show_custom_field_values()
{
	var td = this.parentNode.parentNode;
	var children = td.childNodes;
	var responseSpan = false;
	for (var i=0; i<children.length; i++)
	{
		if (children[i].className == 'responseSpan')
		{
			responseSpan = children[i];
			continue;
		}
		if (children[i].name == 'select_condition[]')
		{
			selectCondition = children[i];
			continue;
		}
	}
	
	responseSpan.innerHTML = '';
	
	selectCondition.options.selectedIndex = 0;
	selectCondition.disabled = true;
	selectCondition.style.display = 'none';
	
	if (this.value != '')
	{
		selectCondition.disabled = false;
		selectCondition.style.display = '';
		
		var xmlHttp = rst_get_xml_http_object();		
		var url = 'index.php?option=com_rsticketspro&controller=kbrules&task=showCustomFieldValues&cfid=' + this.value;

		xmlHttp.onreadystatechange = function() {
			if (xmlHttp.readyState==4)
			{
				has_options = false;
				
				var select = document.createElement('select');
				select.name = 'select_value[]';
				select.disabled = true;
				select.style.display = 'none';
				try {
					var options = eval(xmlHttp.responseText);
					if (options)
						for (var i=0; i<options.length; i++)
						{
							has_options = true;
							var option = document.createElement('option');
							option.value = options[i].id;
							option.text = options[i].name;
							select.options.add(option);
						}
				}
				catch (e)
				{
					alert(e);
				}
				
				if (has_options)
					responseSpan.appendChild(select);
				
				var textbox = document.createElement('input');
				textbox.type = 'text';
				textbox.name = 'select_value[]';
				textbox.disabled = true;
				textbox.style.display = 'none';
				textbox.value = '';
				
				responseSpan.appendChild(textbox);
			}
		};
		
		xmlHttp.open('GET', url, true);
		xmlHttp.send(null);
	}
}

function rst_add_space(child, text)
{
	var space = document.createElement('span');
	if (!text)
		text = '&nbsp;';
	space.innerHTML = text;
	child.appendChild(space);
}

function rst_add_condition()
{
	var child = document.createElement('p');
	
	child.innerHTML = '<?php echo JText::_('RST_IF', true); ?> ';
	
	var select_type = document.createElement('select');
	select_type.name = 'select_type[]';
	select_type.onchange = rst_change_select_type;
	var type_options = [
		{value: '', text: '<?php echo JText::_('RST_PLEASE_SELECT', true); ?>'},
		{value: 'department', text: '<?php echo JText::_('RST_DEPARTMENT', true); ?>'},
		{value: 'subject', text: '<?php echo JText::_('RST_TICKET_SUBJECT', true); ?>'},
		{value: 'message', text: '<?php echo JText::_('RST_TICKET_MESSAGE', true); ?>'},
		{value: 'priority', text: '<?php echo JText::_('RST_PRIORITY', true); ?>'},
		{value: 'status', text: '<?php echo JText::_('RST_TICKET_STATUS', true); ?>'},
		{value: 'custom_field', text: '<?php echo JText::_('RST_CUSTOM_FIELD', true); ?>'}
	];
	for (var i=0; i<type_options.length; i++)
	{
		var option = document.createElement('option');
		option.value = type_options[i].value;
		option.text = type_options[i].text;
		select_type.options.add(option);
	}
	child.appendChild(select_type);
	
	rst_add_space(child);
	
	var responseSpan2 = document.createElement('span');
	responseSpan2.className = 'responseSpan2';
	child.appendChild(responseSpan2);
	
	rst_add_space(child);
	
	var select_condition = document.createElement('select');
	select_condition.name = 'select_condition[]';
	select_condition.onchange = rst_change_select_condition;
	select_condition.disabled = true;
	select_condition.style.display = 'none';
	var condition_options = [
		{value: '', text: '<?php echo JText::_('RST_PLEASE_SELECT', true); ?>'},
		{value: 'eq', text: '<?php echo JText::_('RST_IS_EQUAL', true); ?>'},
		{value: 'neq', text: '<?php echo JText::_('RST_IS_NOT_EQUAL', true); ?>'},
		{value: 'like', text: '<?php echo JText::_('RST_IS_LIKE', true); ?>'},
		{value: 'notlike', text: '<?php echo JText::_('RST_IS_NOT_LIKE', true); ?>'}
	];
	for (var i=0; i<condition_options.length; i++)
	{
		var option = document.createElement('option');
		option.value = condition_options[i].value;
		option.text = condition_options[i].text;
		select_condition.options.add(option);
	}
	child.appendChild(select_condition);
	
	rst_add_space(child);
	
	var responseSpan = document.createElement('span');
	responseSpan.className = 'responseSpan';
	child.appendChild(responseSpan);
	
	rst_add_space(child);
	
	// Connectors
	
	var select = document.createElement('select');
	select.name = 'select_connector[]';
	option1 = document.createElement('option');
	option1.value = 'AND';
	option1.text = '<?php echo JText::_('RST_AND', true); ?>';
	option2 = document.createElement('option');
	option2.value = 'OR';
	option2.text = '<?php echo JText::_('RST_OR', true); ?>';
	select.options.add(option1);
	select.options.add(option2);
	child.appendChild(select);
	
	rst_add_space(child);
	
	// Remove button
	
	var a = document.createElement('a');
	a.href = 'javascript: void(0);';
	a.onclick = rst_remove_condition;
	
	var image = document.createElement('img');
	image.src = '<?php echo JURI::root(true); ?>/administrator/components/com_rsticketspro/assets/images/minus.png';
	image.style.verticalAlign = 'top';
	
	rst_add_space(child);
	
	a.appendChild(image);
	child.appendChild(a);
	
	document.getElementById('rst_conditions').appendChild(child);
}

function rst_remove_condition()
{
	var thediv = this.parentNode;
	document.getElementById('rst_conditions').removeChild(thediv);
}
-->
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsticketspro&controller=kbrules&task=edit'); ?>" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="admintable">
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_RULE_NAME_DESC'); ?>"><label for="name"><?php echo JText::_('RST_KB_RULE_NAME'); ?></label></span></td>
			<td><input type="text" name="name" value="<?php echo $this->escape($this->row->name); ?>" id="name" size="120" maxlength="255" /></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span><label><?php echo JText::_('RST_KB_RULE_CONDITION'); ?></label></span></td>
			<td>
			<a href="javascript: void(0);" onclick="rst_add_condition();"><span class="hasTip" title="<?php echo JText::_('RST_KB_RULE_CONDITION_DESC'); ?>"><?php echo JHTML::image('administrator/components/com_rsticketspro/assets/images/plus.png', '+'); ?></span></a>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td id="rst_conditions"><?php if (!empty($this->row->conditions)) foreach ($this->row->conditions as $i => $condition) { ?><p><?php echo JText::_('RST_IF'); ?> <?php echo $this->lists['select_type'][$i]; ?><span>&nbsp;</span><span class="responseSpan2"><?php echo $this->lists['select_custom_field_value'][$i]; ?></span><span>&nbsp;</span><?php echo $this->lists['select_condition'][$i]; ?><span>&nbsp;</span><span class="responseSpan"><?php echo $this->lists['select_value'][$i]; ?></span><span>&nbsp;</span><?php echo $this->lists['select_connector'][$i]; ?><span>&nbsp;</span><span>&nbsp;</span><a href="javascript: void(0);" id="delete_condition<?php echo $i; ?>"><?php echo JHTML::image('administrator/components/com_rsticketspro/assets/images/minus.png', '-'); ?></a></p><?php } ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_KB_PUBLISH_TO_CATEGORY_DESC'); ?>"><label for="category_id"><?php echo JText::_('RST_KB_PUBLISH_TO_CATEGORY'); ?></label></span></td>
			<td><?php echo $this->lists['categories']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PUBLISH_ARTICLE_DESC'); ?>"><label for="publish_article"><?php echo JText::_('RST_PUBLISH_ARTICLE'); ?></label></span></td>
			<td><?php echo $this->lists['publish_article']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('RST_PRIVATE_ARTICLE_DESC'); ?>"><label for="private"><?php echo JText::_('RST_PRIVATE_ARTICLE'); ?></label></span></td>
			<td><?php echo $this->lists['private']; ?></td>
		</tr>
		<tr>
			<td width="300" style="width: 300px;" align="right" class="key"><span class="hasTip" title="<?php echo JText::_('PUBLISHED_DESC'); ?>"><label for="published"><?php echo JText::_('PUBLISHED'); ?></label></span></td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
	</table>
</div>

<?php echo JHTML::_('form.token'); ?>
<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="controller" value="kbrules" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="view" value="kbrules" />

<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
</form>

<script type="text/javascript">
<!--
for (var i=0; i<document.getElementsByName('select_type[]').length; i++)
	document.getElementsByName('select_type[]')[i].onchange = rst_change_select_type;
for (var i=0; i<document.getElementsByName('select_condition[]').length; i++)
	document.getElementsByName('select_condition[]')[i].onchange = rst_change_select_condition;
for (var i=0; i<document.getElementsByName('select_custom_field_value[]').length; i++)
	document.getElementsByName('select_custom_field_value[]')[i].onchange = rst_show_custom_field_values;
<?php if (!empty($this->row->conditions)) foreach ($this->row->conditions as $i => $condition) { ?>
	document.getElementById('delete_condition<?php echo $i; ?>').onclick = rst_remove_condition;
<?php } ?>
-->
</script>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>