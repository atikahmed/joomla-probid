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

<div style="padding: 1%" align="center">
<h2><?php echo JText::_('RST_EDITING_MESSAGE'); ?></h2>
<form id="rsticketspro_form" action="<?php echo RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=ticketmessage'); ?>" method="post" name="messageForm">
	<p>
		<label for="message"><span class="hasTip" title="<?php echo JText::_('RST_TICKET_MESSAGE'); ?>"><?php echo JText::_('RST_TICKET_MESSAGE'); ?></span></label>
		<?php if ($this->use_editor) { ?>
			<?php echo $this->editor->display('message', $this->row->message,500,250,70,10); ?>
		<?php } else { ?>
			<textarea cols="70" rows="10" class="text_area" type="text" name="message" id="message"><?php echo $this->escape($this->row->message); ?></textarea>
		<?php } ?>
	</p>
	<p>
		<button type="submit" name="Submit" class="button"><?php echo JText::_('RST_UPDATE'); ?></button>
		<button type="button" name="Close" class="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('RST_CLOSE'); ?></button>
	</p>

<input type="hidden" name="option" value="com_rsticketspro" />
<input type="hidden" name="view" value="ticketmessage" />
<input type="hidden" name="cid" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="task" value="savemessage" />
</form>
</div>

<script type="text/javascript">
	var thevalue = document.getElementById('message').value;
	<?php if (!$this->use_editor) { ?>
	thevalue = htmlspecialchars(thevalue, 'ENT_NOQUOTES');
	thevalue = thevalue.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ '<br />' +'$2');
	<?php } ?>
	
	window.parent.document.getElementById('rst_ticket_message_' + <?php echo $this->row->id; ?>).innerHTML = thevalue;
	
function htmlspecialchars (string, quote_style, charset, double_encode) {
    // http://kevin.vanzonneveld.net
    // +   original by: Mirek Slugen
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Nathan
    // +   bugfixed by: Arno
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous
    // +      input by: Mailfaker (http://www.weedem.fr/)
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +      input by: felix
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // %        note 1: charset argument not supported
    // *     example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
    // *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
    // *     example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
    // *     returns 2: 'ab"c&#039;d'
    // *     example 3: htmlspecialchars("my "&entity;" is still here", null, null, false);
    // *     returns 3: 'my &quot;&entity;&quot; is still here'

    var optTemp = 0, i = 0, noquotes= false;
    if (typeof quote_style === 'undefined' || quote_style === null) {
        quote_style = 2;
    }
    string = string.toString();
    if (double_encode !== false) { // Put this first to avoid double-encoding
        string = string.replace(/&/g, '&amp;');
    }
    string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

    var OPTS = {
        'ENT_NOQUOTES': 0,
        'ENT_HTML_QUOTE_SINGLE' : 1,
        'ENT_HTML_QUOTE_DOUBLE' : 2,
        'ENT_COMPAT': 2,
        'ENT_QUOTES': 3,
        'ENT_IGNORE' : 4
    };
    if (quote_style === 0) {
        noquotes = true;
    }
    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
        quote_style = [].concat(quote_style);
        for (i=0; i < quote_style.length; i++) {
            // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
            if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
            }
            else if (OPTS[quote_style[i]]) {
                optTemp = optTemp | OPTS[quote_style[i]];
            }
        }
        quote_style = optTemp;
    }
    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
        string = string.replace(/'/g, '&#039;');
    }
    if (!noquotes) {
        string = string.replace(/"/g, '&quot;');
    }

    return string;
}
</script>

<?php JHTML::_('behavior.keepalive'); ?>