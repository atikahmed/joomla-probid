function rsm_get_xml_http_object()
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

function rsm_refresh_report()
{
	rsm_select_report();
}

function rsm_select_report()
{
	var report = document.getElementById('report').value;

	document.getElementById('rsm_loading').style.display = 'block';
	document.getElementById('rsm_report').innerHTML = '';
	document.getElementById('rsm_legend_container').style.display = 'none';
	
	document.getElementById('rsm_legend_container').innerHTML = '';
	var el = document.createElement('div');

	el.innerHTML = '';
	el.setAttribute('id', 'rsm_legend');
	document.getElementById('rsm_legend_container').appendChild(el);

	xmlHttp = rsm_get_xml_http_object();

	var url = 'index.php?option=com_rsmembership&view=reports';
	var params = new Array();

	params.push('report=' + report);
	params.push('layout=' + report);
	params.push('format=raw');

	for (i=0; i<document.adminForm.elements.length; i++)
	{
		// don't send an empty value
		if (document.adminForm.elements[i].name.length == 0) continue;
		if (document.adminForm.elements[i].value.length == 0) continue;
		// check if the checkbox is checked
		if (document.adminForm.elements[i].type == 'checkbox' && document.adminForm.elements[i].checked == false) continue;
		// check if the radio is selected
		if (document.adminForm.elements[i].type == 'radio' && document.adminForm.elements[i].checked == false) continue;

		// check if this is a dropdown with multiple selections
		if (document.adminForm.elements[i].type == 'select-multiple')
		{
			for (var j=0; j<document.adminForm.elements[i].options.length; j++)
				if (document.adminForm.elements[i].options[j].selected)
					params.push(document.adminForm.elements[i].name + '=' + escape(document.adminForm.elements[i].options[j].value));

			continue;
		}

		params.push(document.adminForm.elements[i].name + '=' + escape(document.adminForm.elements[i].value));
	}
	params = params.join('&');

	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState==4)
		{
			document.getElementById('rsm_loading').style.display = 'none';
			document.getElementById('rsm_report').innerHTML = xmlHttp.responseText;

			if (document.getElementById('rsm_reports_table'))
			{
				var thewidth = document.getElementById('rsm_reports_table').rows[0].cells.length;
				thewidth = (thewidth - 1) * 60;
			}
			if (thewidth < 950)
				thewidth = 950;
			rsm_build_graph(thewidth, 450);
			document.getElementById('rsm_legend_container').style.display = '';
		}
	}

	xmlHttp.open("POST", url, true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", params.length);
	xmlHttp.setRequestHeader("Connection", "close");

	xmlHttp.send(params);
}

function rsm_hex_to_rgb(h)
{
	h = h.charAt(0)=="#" ? h.substring(1,7) : h;
	r = parseInt(h.substring(0,2),16);
	g = parseInt(h.substring(2,4),16);
	b = parseInt(h.substring(4,6),16);
	
	return [r, g, b];
}