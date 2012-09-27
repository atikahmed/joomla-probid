// ########### Tabfunctions ####################

window.addEvent('domready', function() {
	var alldivs = document.id(document.body).getElements('div.hidetabcontent');
	var outerdivs = document.id(document.body).getElements('div.hidetabouter');
	outerdivs = outerdivs.getProperty('id');

	var countankers_num = 1;
		
	for (var i = 0; i < outerdivs.length; i++) {
		alldivs = document.id(outerdivs[i]).getElements('div.hidetabcontent');
		count = 0;
		alldivs.each(function(element) {
			count++;
			var el = document.id(element);
			el.setProperty('role', 'tabpanel');
			el.setProperty('aria-hidden', 'true');
			el.setProperty('aria-expanded', 'false');
			elid = el.getProperty('id');
			elid = elid.split('_');
			elid = 'link_' + elid[1];
			el.setProperty('aria-labelledby', elid);

			if (count == countankers_num) {
				el.addClass('tabopen').removeClass('tabclosed');
				el.setProperty('aria-hidden', 'false');
				el.setProperty('aria-expanded', 'true');
			}
		});

		countankers = 0;
		allankers = document.id(outerdivs[i]).getElement('ul.hidetabs').getElements('a');
		
		allankers.each(function(element) {
			countankers++;
			var el = document.id(element);
			el.setProperty('aria-selected', 'true');
			el.setProperty('role', 'tab');
			linkid = el.getProperty('id');
			moduleid = linkid.split('_');
			moduleid = 'hidemodule_' + moduleid[1];
			el.setProperty('aria-controls', moduleid);

			if (countankers != countankers_num) {
				el.addClass('linkclosed').removeClass('linkopen');
				el.setProperty('aria-selected', 'false');
			}
		});
	}
});


function hidetabshow(elid) {
	var el = document.id(elid);
	if(el != null)
	{
		var outerdiv = el.getParent();

		outerdiv = outerdiv.getProperty('id');

		var alldivs = document.id(outerdiv).getElements('div.hidetabcontent');
		var liste = document.id(outerdiv).getElement('ul.hidetabs');

		liste.getElements('a').setProperty('aria-selected', 'false');

		alldivs.each(function(element) {
			element.addClass('tabclosed').removeClass('tabopen');
			element.setProperty('aria-hidden', 'true');
			element.setProperty('aria-expanded', 'false');
		});

		el.addClass('tabopen').removeClass('tabclosed');
		el.setProperty('aria-hidden', 'false');
		el.setProperty('aria-expanded', 'true');
		//el.focus();
		var getid = elid.split('_');
		var activelink = 'link_' + getid[1];
		document.id(activelink).setProperty('aria-selected', 'true');
		liste.getElements('a').addClass('linkclosed').removeClass('linkopen');
		document.id(activelink).addClass('linkopen').removeClass('linkclosed');
	}
}
