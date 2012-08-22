// XML HTTP Object
function rst_get_xml_http_object()
{
	var xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

function rst_flag_ticket(url, button, ticket_id)
{
	// no flag
	if (button.className == 'rst_flag')
	{
		button.className = 'rst_flag rst_flag_active';
		flagged = 1;
	}
	else
	{
		button.className = 'rst_flag';
		flagged = 0;
	}
	
	xmlHttp = rst_get_xml_http_object();
	
	params  = 'option=com_rsticketspro';
	params += '&controller=ticket';
	params += '&task=flag';
	params += '&cid=' + ticket_id;
	params += '&flagged=' + flagged;
	xmlHttp.open("POST", url, true);
	
	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.send(params);
}

function rst_feedback(url, value, ticket_id)
{
	if (window.rsticketspro_rating.options.disabled)
		return false;
	
	rst_feedback_message();
	
	xmlHttp = rst_get_xml_http_object();
	
	params  = 'option=com_rsticketspro';
	params += '&controller=ticket';
	params += '&task=feedback';
	params += '&cid=' + ticket_id;
	params += '&feedback=' + value;
	xmlHttp.open("POST", url, true);
	
	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");
	
	xmlHttp.send(params);
	
	window.rsticketspro_rating.options.disabled = true;
}

var rst_buffer;

function rst_search(value)
{
	if (value.length == 0)
	{
		rst_close_search();
		return;
	}
	
	if (rst_buffer)
		clearTimeout(rst_buffer);
	rst_buffer = setTimeout(function() { rst_search_ajax(value); }, 300);
}

function rst_search_ajax(value)
{
	xmlHttp = rst_get_xml_http_object();
	
	var url = 'index.php?option=com_rsticketspro&task=kbsearch';
	url += '&filter=' + value;
	url += '&sid=' + Math.random();
	xmlHttp.onreadystatechange = function() {
			if (xmlHttp.readyState==4)
			{
				document.getElementById('rst_livesearch').innerHTML = xmlHttp.responseText;
				document.getElementById('rst_livesearch').style.border = '1px solid #A5ACB2';
				document.getElementById('rst_livesearch').style.display = '';
			}
		}
	xmlHttp.open("GET", url, true);
	xmlHttp.send(null);
}

function rst_close_search()
{
	document.getElementById('rst_search_value').value = '';
	document.getElementById('rst_livesearch').style.display = 'none';
	document.getElementById('rst_livesearch').innerHTML = '';
	document.getElementById('rst_livesearch').style.border = '0px';
	
	return false;
}