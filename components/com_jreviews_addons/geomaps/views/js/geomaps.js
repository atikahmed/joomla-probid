/**
* GeoMaps Addon for JReviews
* Copyright (C) 2010-2012 ClickFWD LLC
* This is not free software, do not distribute it.
* For licencing information visit http://www.reviewsforjoomla.com
* or contact sales@reviewsforjoomla.com
**/

function __geomaps_api_loaded() {

    if(GeomapsOnload !== undefined) {

        for (i = 0; i < GeomapsOnload.length; i++) {
            GeomapsOnload[i]();
        }

    }
}

(function () {

    function async_load() {
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = GeomapsGoogleApi + '&callback=__geomaps_api_loaded';
        var x = document.getElementsByTagName('script')[0];
        x.parentNode.insertBefore(s, x);
    }

    if (window.attachEvent)
        window.attachEvent('onload', async_load);
    else
        window.addEventListener('load', async_load, false);

})();


// Mapping function for results, module and detail pages
function GeomapsDisplayMap(mapCanvas, params) {

    // private members

    var data_ = {'infowindow': '', 'count': 0, 'icons': {}, 'payload': {}};

    var config_ = [];

    var icons_ = {};

    var gicons_ = [];

    var defaultIcon = null;

    var directionsService_ = null;

    var directionsDisplay_ = null;

    var map = null;

    var mapCanvas_ = mapCanvas;

    var mapTooltip_ = mapCanvas_ + 'Tooltip';

    var mapTypes_ = {'G_NORMAL_MAP': google.maps.MapTypeId.ROADMAP, 'G_HYBRID_MAP': google.maps.MapTypeId.HYBRID, 'G_SATELLITE_MAP': google.maps.MapTypeId.SATELLITE, 'G_PHYSICAL_MAP': google.maps.MapTypeId.TERRAIN};

    var markers_ = [];

    var markerClusterer = null;

    var infoWindow_ = null;

    var bounds = new google.maps.LatLngBounds();

    var clustering_ = false;

    var clusteringMinMarkers_ = 250; // number of markers to trigger clustering when clustering enabled

    var directions_ = false;

    var panoClient_ = null;

    var myPano_ = null;

    var streetViewStatus_ = false;

    var lastLatLng_ = null;

    var markerClickTracker_ = false;

	var center = null;

	var zoom = null;

	var defaults = {

		'search_bar': false
	}

	var options = jQuery.extend(defaults, params);

    if (typeof options === "object" && options !== null) {

        if (typeof options.clustering === "boolean") {

            clustering_ = options.clustering;

        }

        if (typeof options.clusteringMinMarkers === "number" && options.clusteringMinMarkers > 0) {

            clusteringMinMarkers_ = options.clusteringMinMarkers;

        }

        if (typeof options.directions === "boolean") {

            directions_ = options.directions;

        }

        if (typeof options.streetview === "boolean") {

            streetViewStatus_ = options.streetview;

        }

    }

    icons_['default'] = {

        'type': 'custom',

        'url': 'http://chart.apis.google.com/chart?chst=d_map_spin&chld=0.6|0|FE766A|12|_|',

        'size': [23, 41]

    };

    icons_['default_hover'] = {

        'type': 'custom',

        'url': 'http://chart.apis.google.com/chart?chst=d_map_spin&chld=0.6|0|FDFF0F|12|_|',

        'size': [23, 41]

    };

    icons_['default_featured'] = {

        'type': 'custom',

        'url': 'http://chart.apis.google.com/chart?chst=d_map_spin&chld=0.6|0|5F8AFF|12|_|',

        'size': [23, 41]

    };

    icons_['numbered'] = {

        'type': 'numbered',

        'url': 'http://chart.apis.google.com/chart?chst=d_map_spin&chld=0.6|0|FE766A|12|_|{index}',

        'size': [23, 41]

    };

    icons_['numbered_hover'] = {

        'type': 'numbered',

        'url': 'http://chart.apis.google.com/chart?chst=d_map_spin&chld=0.6|0|FDFF0F|12|_|{index}',

        'size': [23, 41]

    };

    icons_['numbered_featured'] = {

        'type': 'numbered',

        'url': 'http://chart.apis.google.com/chart?chst=d_map_spin&chld=0.6|0|5F8AFF|12|_|{index}',

        'size': [23, 41]

    };

    icons_['numbered_featured_hover'] = {

        'type': 'numbered',

        'url': 'http://chart.apis.google.com/chart?chst=d_map_spin&chld=0.6|0|FDFF0F|12|_|{index}',

        'size': [23, 41]

    };


    this.init = function () {

		var $_this = this;

		var mapDiv = jQuery('#' + mapCanvas_);

		var tab = mapDiv.parents('.jr_tabs');

		// If the map is inside a jQuery tab we delay the initialization until the tab is shown
		if(tab.length && tab.data('mapInit') == undefined)
		{
			tab.data('mapInit',1);

			tab.bind('tabsshow', function(event, ui) {

				if (ui.panel.id == mapDiv.parents('.ui-tabs-panel').eq(0).attr('id') /* tab id */) {

					$_this.init();

				}

			});

			return true;

		}

		if (map == null) {

            map = new google.maps.Map(document.getElementById(mapCanvas_), {

                zoom: 15,

                // center: new google.maps.LatLng(lat, lon),

                mapTypeId: google.maps.MapTypeId.ROADMAP

            });

            map.setOptions(setOptions());

            map.setMapTypeId(mapTypes_[data_.mapUI.maptypes.def]);

			if(options.search_bar) {
				this.showAddressBar();
			}

            google.maps.event.addListener(map, "idle", function () {

                mapDiv.removeClass('jrMapLoading')
		       		  .css({'overflow-x':'visible','overflow-y':'visible'}); // for callout and custom info windows

            });

			// Close non-google infowindow

            google.maps.event.addListener(map, "dragstart", function () {

                closeTooltip();

            });

            google.maps.event.addListener(map, "movestart", function () {

                closeTooltip();

            });

            google.maps.event.addListener(map, "moveend", function () {

                closeTooltip();

            });

            google.maps.event.addListener(map, "dblclick", function () {

                closeTooltip();

            });

            google.maps.event.addListener(map, "zoom_changed", function () {

                closeTooltip();

            });

            // Load custom icons
            for (var i in data_.icons) {

                icons_[i] = data_.icons[i];

            }

            // Auto-disable clustering based on min clustering markers
            if (clustering_ == true && data_.count > clusteringMinMarkers_) {

                for (var i in data_.payload) {

                    var latlng = new google.maps.LatLng(data_.payload[i].lat, data_.payload[i].lon);

                    bounds.extend(latlng);

                    markers_.push(createMarker(latlng, data_.payload[i])); // Must use markers_.push or cluseting doesn't work otherwise

                }

            } else if (data_.count > 0) {

                for (var i in data_.payload) {

                    var latlng = new google.maps.LatLng(data_.payload[i].lat, data_.payload[i].lon);

                    bounds.extend(latlng);

                    markers_[i] = createMarker(latlng, data_.payload[i]);

                    markers_[i].setMap(map);

                }

            }

			this.moveCenterAndZoom();

			if (clustering_ == true && data_.count > clusteringMinMarkers_) {
	            refreshMap(markers_);
			}

            // Initialize directions
            if (directions_ == true) {

                directionsService_ = new google.maps.DirectionsService();

                directionsDisplay_ = new google.maps.DirectionsRenderer({

                    map: map,

                    panel: document.getElementById(mapCanvas_ + '_results')

                });

            }

            panoClient_ = new google.maps.StreetViewService();

            myPano_ = new google.maps.StreetViewPanorama(document.getElementById(mapCanvas + '_streetview'), {visible: false});

            if (streetViewStatus_ == true) {

                // Trigger streetview on current location

                panoClient_.getPanoramaByLocation(map.getCenter(), 50, showPanoData);


                // Enables streetview changes on map clicks, not just marker clicks

                google.maps.event.addListener(map, "click", function (eventArgs) {

                    if (markerClickTracker_ == false) {

                        panoClient_.getPanoramaByLocation(eventArgs.latLng, 50, showPanoData);

                    }

                    markerClickTracker_ = false;

                });

            }

        } else {

            google.maps.event.trigger(map, 'resize');

            google.maps.event.trigger(myPano_, 'resize');

			this.moveCenterAndZoom();

        }
    }

    this.addMarker = function (marker) {data_.payload.push(marker);}

    this.setCount = function (count) {data_.count = count;}

    this.setData = function (data) {

        data_ = data;

        if (undefined == data_.infowindow) data_.infowindow = 'google';

    }

    this.getData = function () {return data_;}

    this.getCount = function () {return data_.count;}

    this.addIcon = function (icon) {icons_.push(icon);}

    this.getMap = function () {return map;}

    this.findCenter = function () {

        var center_lat = (bounds.getNorthEast().lat() + bounds.getSouthWest().lat()) / 2.0;

        var center_lng = (bounds.getNorthEast().lng() + bounds.getSouthWest().lng()) / 2.0;

        if (bounds.getNorthEast().lng() < bounds.getSouthWest().lng()) {

            center_lng += 180;

        }

        return new google.maps.LatLng(center_lat, center_lng)

    }

    this.panToCenter = function () {

        map.panTo(this.findCenter());

    }

    this.centerAndZoomOnBounds = function () {

        var zoom;

        if (data_.mapUI.zoom.start != '') {

            zoom = data_.mapUI.zoom.start;

            map.setZoom(zoom);

        } else {

            for (var i = 0; i < markers_.length; i++) {

                bounds.extend(markers_[i].getPosition());

            }

            map.fitBounds(bounds);

        }

        map.setCenter(this.findCenter());

    }

    this.toggleSizeMap = function (object, width) {

        var streetView = jQuery('#' + mapCanvas_ + '_streetview');

        if (jQuery('#' + mapCanvas).width() <= width) {

            jQuery('#gm_resizeL').hide('fast', function () {jQuery(this).css('display', 'none');});

            jQuery('#gm_resizeS').show('fast', function () {jQuery(this).css('display', '');});

            jQuery('#gm_mapColumn').animate({width: 600}, "slow");

            jQuery('#' + mapCanvas_ + '_above').animate({width: 600}, "slow");

            if (streetView.css('display') == 'none') {

                streetView.css('width', 600)

            } else if (streetView.css('display') != '') {

                streetView.animate({width: 600}, "slow", function () {if (lastLatLng_ != null) panoClient_.getPanoramaByLocation(lastLatLng_, 50, showPanoData);});

            }

            jQuery('#' + mapCanvas_).animate({width: 600, height: 500}, "slow", function () {object.init();});

        } else {

            jQuery('#gm_resizeS').hide('fast', function () {jQuery(this).css('display', 'none');});

            jQuery('#gm_resizeL').show('fast', function () {jQuery(this).css('display', '');});

            jQuery('#gm_mapColumn').animate({width: width}, "slow");

            jQuery('#' + mapCanvas_ + '_above').animate({width: width}, "slow");

            if (streetView.css('display') == 'none') {

                streetView.css('width', width)

            } else if (streetView.css('display') != '') {

                streetView.animate({width: width}, "slow", function () {if (lastLatLng_ != null) panoClient_.getPanoramaByLocation(lastLatLng_, 50, showPanoData);});

            }

            jQuery('#' + mapCanvas_).animate({width: width, height: width}, "slow", function () {object.init();});

        }

    }

    function setOptions() {

        var customUI = {};

        var mapTypeIds = [];

        data_.mapUI.maptypes.map && mapTypeIds.push(google.maps.MapTypeId.ROADMAP);

        data_.mapUI.maptypes.hybrid && mapTypeIds.push(google.maps.MapTypeId.HYBRID);

        data_.mapUI.maptypes.satellite && mapTypeIds.push(google.maps.MapTypeId.SATELLITE);

        data_.mapUI.maptypes.terrain && mapTypeIds.push(google.maps.MapTypeId.TERRAIN);

        customUI.mapTypeControlOptions = {mapTypeIds: mapTypeIds};

        customUI.scrollwheel = data_.mapUI.zoom.scrollwheel;

        customUI.disableDoubleClickZoom = !data_.mapUI.zoom.doubleclick;

        // Disable StreetView control
        customUI.streetViewControl = false;

        // Scale control
        customUI.scaleControl = data_.mapUI.controls.scalecontrol;

        // Pan control
        customUI.panControl = data_.mapUI.controls.largemapcontrol3d;

        // Zoom control & style
        customUI.zoomControl = false;

        if (data_.mapUI.controls.largemapcontrol3d || data_.mapUI.controls.smallzoomcontrol3d) {

            customUI.zoomControl = true;

            if (data_.mapUI.controls.smallzoomcontrol3d) {

                customUI.zoomControlOptions = {style: google.maps.ZoomControlStyle.SMALL};

            } else {

                customUI.zoomControlOptions = {style: google.maps.ZoomControlStyle.LARGE};

            }

        }

        // Map type control & style

        customUI.mapTypeControl = false;

        if (data_.mapUI.controls.maptypecontrol || data_.mapUI.controls.menumaptypecontrol) {

            customUI.mapTypeControl = true;

            if (data_.mapUI.controls.menumaptypecontrol) {

                customUI.mapTypeControlOptions.style = google.maps.MapTypeControlStyle.DROPDOWN_MENU;

            } else {

                customUI.mapTypeControlOptions.style = google.maps.MapTypeControlStyle.HORIZONTAL_BAR;

            }

        }

        return customUI;

    }

    function refreshMap() {

        if (markerClusterer != null) {

            markerClusterer.clearMarkers();

        }

        var zoom = 15;

        var size = 30; // Grid size of a cluster, the higher the quicker

        //var style = document.getElementById("style").value;

        zoom = zoom == -1 ? null : zoom;

        size = size == -1 ? null : size;

        //style = style == "-1" ? null: parseInt(style, 10);

        //markerClusterer = new MarkerClusterer(map, markers, {maxZoom: zoom, gridSize: size, styles: styles[style]});

        markerClusterer = new MarkerClusterer(map, markers_, {maxZoom: zoom, gridSize: size});

    }


    function createMarker(latlng, markerData) {

        if (markerData.icon == '' || markerData.icon == undefined) markerData.icon = 'default';

        if (markerData.featured == 1 && undefined != icons_[markerData.icon + '_featured']) {

            markerData.icon = markerData.icon + '_featured';

        }

        var icon = makeIcon(markerData.icon, markerData.index);

        var marker = new google.maps.Marker({

            position: latlng,

            icon: icon,

            title: markerData.title

        });

        icon.data && icon.data.shadow && marker.setShadow(icon.data.shadow);

        marker.icon_name = markerData.icon;

        marker.id = markerData.id;

        marker.data = markerData;

        marker.data.latlng = latlng;

        google.maps.event.addListener(marker, "click", function () {

            showTooltip(marker);

            if (streetViewStatus_ == true) {

                panoClient_.getPanoramaByLocation(latlng, 50, showPanoData);

                lastLatLng_ = latlng;

                markerClickTracker_ = true;

            }

        });

        google.maps.event.addListener(marker, "mouseover", function () {

            switchMarkerImage(marker, '_hover');

            return false;

        });

        google.maps.event.addListener(marker, "mouseout", function () {

            switchMarkerImage(marker, '');

        });



        return marker;

    }

    function makeIcon(name, index) {

        if (gicons_.length && undefined != gicons_[name] && name != 'numbered') {

            return gicons_[name];

        }

        if (undefined == icons_[name]) name = 'default';

        var icon;



        switch (icons_[name].type) {

            case 'custom':

                icon = makeCustomIcon(icons_[name]);

                break;

            case 'numbered':

            case 'numbered_featured':

                icon = makeNumberedIcon(icons_[name], index);

                break;

            case 'default':

            default:

                icon = makeDefaultIcon(icons_[name]);

                break;

        }

        gicons_[name] = icon;

        return icon;

    }

    function makeCustomIcon(iconData) {

        var customIcon = new google.maps.MarkerImage(iconData.url);

        customIcon.size = new google.maps.Size(iconData.size[0], iconData.size[1]);



        return customIcon;

    }

    function makeDefaultIcon(iconData) {

        var defaultIcon = new google.maps.MarkerImage(iconData.url);

        defaultIcon.size = new google.maps.Size(23, 41);

        defaultIcon.data = {shadow: new google.maps.MarkerImage("http://www.google.com/mapfiles/shadow50.png", new google.maps.Size(37, 34), null, new google.maps.Point(9, 37))};

        var icon = new google.maps.MarkerImage(iconData.url, defaultIcon.size);

        icon.data = defaultIcon.data;

        return icon;

    }

    function makeNumberedIcon(iconData, index) {

        if (null == defaultIcon) {

            defaultIcon = new google.maps.MarkerImage(iconData.url);

            defaultIcon.size = new google.maps.Size(23, 41);

            defaultIcon.data = {shadow: new google.maps.MarkerImage("http://www.google.com/mapfiles/shadow50.png", new google.maps.Size(37, 34), null, new google.maps.Point(9, 37))};

        }

        var icon;

        if (index != '') {

            icon = new google.maps.MarkerImage(iconData.url.replace('{index}', (0 + index)), defaultIcon.size);

        } else {

            icon = new google.maps.MarkerImage(iconData.url.replace('{index}', ''), defaultIcon.size);

        }

        icon.data = defaultIcon.data;

        return icon;

    }

    function switchMarkerImage(marker, status) {

        if (undefined != marker && icons_[marker.icon_name + status]) {

            marker.setIcon(icons_[marker.icon_name + status].url.replace('{index}', marker.data.index));

        }

    }

    this.switchMarkerImageById = function (id, status) {

        switchMarkerImage(markers_['id' + id], status);

    }

    function renderTooltip(data, useTabs) {

        // Standard fields

        var roundDecimals = 1;

        var infoWindowContainer = jQuery('#gm_infowindowContainer').clone();

        var infoWindow = infoWindowContainer.find('.gm_infowindow');

        if (data_.mapUI.title.trim == true && data_.mapUI.title.trimchars > 0) {

            data.title = truncate(data.title, data_.mapUI.title.trimchars);

        }

        infoWindow.find('.gm-title').html(data.title);

        infoWindow.find('.gm-title').attr('href', data.url);

        if (false != data.image) {

            infoWindow.find('.gm_image').html('<img class="gm-image" src="' + data.image + '" />');

        }

		// Process ratings
		var user_rating = infoWindow.find('.overall_user');

		var editor_rating = infoWindow.find('.overall_editor');

		var rating_div = infoWindow.find('.overall_ratings');

		if(data.rating_scale === undefined) {
			rating_div.hide();
		}
		else {

			rating_div.show();

			if(data.user_rating === undefined)
			{
				user_rating.hide();
			}
			else {
				user_rating.show();

				infoWindow.find('.gm-user-rating-star').css('width', (data.user_rating / data.rating_scale) * 100 + '%');

				infoWindow.find('.gm-user-rating-value').html(Math.round(0 + (data.user_rating) * Math.pow(10, roundDecimals)) / Math.pow(10, roundDecimals));

				infoWindow.find('.gm-user-rating-count').html(parseInt(0 + data.user_rating_count));
			}

			if(data.editor_rating === undefined)
			{
				editor_rating.css('display','none');
			}
			else {
				editor_rating.show();

				infoWindow.find('.gm-editor-rating-star').css('width', (data.editor_rating / data.rating_scale) * 100 + '%');

				infoWindow.find('.gm-editor-rating-value').html(Math.round(0 + (data.editor_rating) * Math.pow(10, roundDecimals)) / Math.pow(10, roundDecimals));
			}

		}

		for (var i in data.field) {

            infoWindow.find('.gm-' + i).html(data.field[i]);

        }

        /*if (useTabs == false) {

        return infoWindowContainer.html();

        } else {

        var tabs = [];

        infoWindowContainer.find('div.gm_tab').each(function () {

        tabs.push(new GInfoWindowTab(jQuery(this).attr('title'), jQuery(this).html()));

        });

        return tabs;

        }*/

        return infoWindowContainer.html(); // tabs not implemented

    }



    this.showTooltipById = function (id) {

        showTooltip(markers_['id' + id]);

    }



    this.closeTooltip = function () {

        closeTooltip();

    }

    function showTooltip(marker) {

        if (undefined != marker) {

            switch (data_.infowindow) {

                case 'google':

                case 'google_tabs':

                    if (null == infoWindow_) {

                        infoWindow_ = new google.maps.InfoWindow();

                        google.maps.event.addListener(infoWindow_, "closeclick", function () {

                            markerClickTracker_ = true;

                        });

                    }

                    infoWindow_.setContent(renderTooltip(marker.data, false));

                    infoWindow_.open(map, marker);

                    break;

                //    marker.openInfoWindowTabsHtml(renderTooltip(marker.data, true));

                //    break; // Tabs window doesn't exists in v3

                default:

                    if (jQuery('#' + mapTooltip_).length == 0) {

                        jQuery("#" + mapCanvas_).append('<div id="' + mapTooltip_ + '" class="gm_mapInfowindow"></div>');

                    }

                    var tooltip = jQuery('#' + mapTooltip_);

                    tooltip.html('');

                    tooltip.marker = marker;

                    closeTooltip();

                    tooltip.html(renderTooltip(marker.data, false));

                    // Attach close onclick event

                    jQuery('.gm_infowindow').find('.gm-close-tooltip').unbind().click(function () {closeTooltip();});

                    positionTooltip(tooltip);

                    break;

            }

        }

    }

    function positionTooltip(tooltip) {

        var mapBounds = map.getBounds();

        if (!mapBounds.contains(tooltip.marker.getPosition())) {

            map.setCenter(tooltip.marker.getPosition());

        }

        // Get relative positioning for tooltip

        var scale = Math.pow(2, map.getZoom());

        var nw = new google.maps.LatLng(

            map.getBounds().getNorthEast().lat(),

            map.getBounds().getSouthWest().lng()

        );

        var worldCoordinateNW = map.getProjection().fromLatLngToPoint(nw);

        var worldCoordinate = map.getProjection().fromLatLngToPoint(tooltip.marker.getPosition());

        var pointDivPixel = new google.maps.Point(

            Math.floor((worldCoordinate.x - worldCoordinateNW.x) * scale),

            Math.floor((worldCoordinate.y - worldCoordinateNW.y) * scale)

        );

        tooltip.css('left', parseInt(pointDivPixel.x - 375 + parseInt(data_.mapUI.anchor.x)) + 'px');

        tooltip.css('top', parseInt(pointDivPixel.y - 102 + parseInt(data_.mapUI.anchor.y)) + 'px');

        tooltip.fadeIn('slow');

    }


    function closeTooltip() {

        jQuery('#' + mapTooltip_).css('display', 'none');

        return false;

    }

    function truncate(text, len) {

        if (text.length > len) {

            var copy;

            text = copy = text.substring(0, len);

            text = text.replace(/\w+$/, '');

            if (text == '') text = copy;

            text += '...';

        }

        return text;

    }

    /******************************
    * Street view functions
    ******************************/
    this.setStreetView = function (status) {

        streetViewStatus_ = status;

    }

    this.getStreetViewById = function (id) {

        if (streetViewStatus_ == true) {

            var streetView = jQuery('#' + mapCanvas_ + '_streetview');

            if (streetView.css('display') != 'block') streetView.slideDown();

            panoClient_.getPanoramaByLocation(markers_['id' + id].data.latlng, 50, showPanoData);

            lastLatLng_ = markers_['id' + id].data.latlng;

        }

    }

    function showPanoData(panoData, status) {

		jQuery('#gm_streetview_msg').remove();

		if (status != google.maps.StreetViewStatus.OK) {

            jQuery('#' + mapCanvas_ + '_streetview').before('<div id="gm_streetview_msg" style="margin:10px;">' + GeomapsLanguage['streeview_unavailable'] + '</div>');

        }

		else {

			myPano_.setPosition(panoData.location.latLng);

			myPano_.setVisible(true);

		}

    }

    function handleNoFlash(errorCode) {

        if (errorCode == 603) {

            jQuery('#' + mapCanvas_ + '_streetview').html('<div id="gm_streetview_msg" style="margin:10px;">Flash doesn\'t appear to be supported by your browser.</div>');

            return;

        }
    }


    /******************************
    * Get direction functions
    ******************************/
    function handleDirectionResult(result, status) {

        switch (status) {

            case google.maps.DirectionsStatus.INVALID_REQUEST:

                showMessage("A directions request could not be successfully parsed.\n Error code: " + status);

                break;

            case google.maps.DirectionsStatus.OK:

                directionsDisplay_.setDirections(result);

                break;

            case google.maps.DirectionsStatus.UNKNOWN_ERROR:

                showMessage("A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.\n Error code: " + status);

                break;

            case google.maps.DirectionsStatus.NOT_FOUND:

            default:

                showMessage(GeomapsLanguage["directions_request_error"]);

                break;

        }

    }

    this.getDirections = function (fromAddress, toAddress, locale) {

        hideErrorMessage();

        var results = jQuery('#' + mapCanvas_ + '_results');

        if (results.css('display') == 'none' && jQuery('#' + mapCanvas_).css('width') == '100%') // Change map width and bring direction results into view

        {

            var directionsWidth = results.width();

            var mapWidth = parseInt(jQuery('#' + mapCanvas_).width() - directionsWidth - 20);

            jQuery('#' + mapCanvas_).css('width', mapWidth + 'px');

        }

        jQuery('#' + mapCanvas_ + '_results').show();


        var travelMode = google.maps.TravelMode.DRIVING;

        switch (parseInt(jQuery('#gm_direction_travelmode').val())) {

            case 0:travelMode = google.maps.TravelMode.DRIVING;break;

            case 2:travelMode = google.maps.TravelMode.WALKING;break;

            case 4:travelMode = google.maps.TravelMode.BICYCLING;break;

        }

        var request = {

            origin: fromAddress,

            destination: toAddress,

            region: locale,

            travelMode: travelMode

        };

        directionsService_.route(request, handleDirectionResult);

    }

    this.swapInputs = function () {

        var tmp = jQuery('#from_point').val();

        jQuery('#from_point').val(jQuery('#to_point').val());

        jQuery('#to_point').val(tmp);

    }

    function showMessage(text) {

        jQuery('#' + mapCanvas_ + '_results').html(text).fadeIn();

    }

    function hideErrorMessage() {

        jQuery('#' + mapCanvas_ + '_results').html('');

    }

    this.setCenterAndZoom = function (lat, lon, zoom) {

		this.center = new google.maps.LatLng(lat, lon);
        this.zoom = zoom;
    }

	this.moveCenterAndZoom = function()
	{
		if(this.center != null && this.zoom != null) {
			map.setZoom(this.zoom);
	        map.setCenter(this.center);
		}
		else {
            this.centerAndZoomOnBounds();
		}
	}

	this.showAddressBar = function()
	{
		var mapGeocoder = new google.maps.Geocoder(),
			search_bar = document.createElement('div');

		search_bar.innerHTML =
			'<div class="geomapsAddressBar">'
			+ '<input class="geomaps-address" type="text" size="50" name="gm_module_address" placeholder="'+GeomapsLanguage["enter_location"]+'" />'

			+ '<input type="button" class="jrButton" value="'+GeomapsLanguage["submit"] +'" />'

			+ '</div>'
		;

		map.controls[google.maps.ControlPosition.TOP_LEFT].push(search_bar);

		var searchBar = jQuery(search_bar).css({'z-index':1,'-moz-user-select':''});

		var addressField = jQuery('input[type=text]', searchBar);

		var submitButton = jQuery('input[type=button]', searchBar);

		addressField.keyup(function(e) {
			if(e.keyCode == 13) {
			  submitButton.trigger('click');
			}
		});

		submitButton.bind('click',function()
		{
			var address = addressField.val();

			if(address == '') return false;

	        mapGeocoder.geocode({address: address}, function(results, status)
			{
				if (google.maps.GeocoderStatus.OK == status) {

					var point = results[0].geometry.location;

					map.setZoom(11);

					map.setCenter(point);

				} else {

					// Not found
				}
			});

		});
	}
}

geomaps =
{

    map: null,

    geocoder: null,

    marker: null,

    infoWindow: null,

    coordinates: ['', ''],

    autocomplete: null,

    initializeMap: function (lat, lon) {

        var options = {

            zoom: 15,

            center: new google.maps.LatLng(lat, lon),

            mapTypeId: google.maps.MapTypeId.ROADMAP

        };

		var mapDiv = jQuery('#gm_mapPopupCanvas');

        geomaps.map = new google.maps.Map(document.getElementById('gm_mapPopupCanvas'), options);

        geomaps.marker = new google.maps.Marker({

            position: new google.maps.LatLng(lat, lon),

            draggable: true

        });

		google.maps.event.addListener(geomaps.map, "idle", function () {

			mapDiv.removeClass('jrMapLoading');

		});

        google.maps.event.addListener(geomaps.marker, "dragstart", function () {

            geomaps.infoWindow && geomaps.infoWindow.close();

        });

        google.maps.event.addListener(geomaps.marker, "dragend", function () {

            jQuery('#' + jr_lat).val(geomaps.marker.getPosition().lat());

            jQuery('#' + jr_lon).val(geomaps.marker.getPosition().lng());

        });

        geomaps.marker.setMap(geomaps.map);

    },

    _getInputFieldValue: function (id, form)
	{
        var $input = jQuery('#' + id, form);

        try {

			var type = $input.prop('type');
        }
		catch (err) {

			var type = $input.attr('type');
        }

        switch (type)
		{
            case 'select-one':
                var input = jQuery('#' + id + ' option:selected', form);
                inputVal = input.val() != '' ? jQuery('#' + id + ' option:selected', form).text() : null;
                break;

            default:
                inputVal = jQuery('#' + id, form).val();
                break;
        }
        return inputVal;
    },

	mapAddLatLonFields: function(form, lat, lon)
	{
		var lat_selector = ':input[name="data[Field][Listing]['+jr_lat+']]',

			latInput = jQuery('<input type="hidden" name="data[Field][Listing][' + jr_lat + ']" />'),

			lonInput = jQuery('<input type="hidden" name="data[Field][Listing][' + jr_lon + ']" />')

			;

		// If coordinate inputs not already in the form, add them
        if(jQuery(lat_selector, form).length == 0)
		{

			latInput.appendTo(form);

            lonInput.appendTo(form);

        }

		latInput.val(lat);

		lonInput.val(lon);
	},

	mapSearchAddress: function (element, callback, e)
	{
        var searchButton = jQuery(element),

			formId = searchButton.parents('form:eq(0)').attr('id'),

			parentForm = jQuery('#' + formId),

			address = [],

			inputVal = ''

			;

        if (undefined != geoDistanceSearchAddress) {

            inputVal = geomaps._getInputFieldValue(geoDistanceSearchAddress, parentForm);

            if (null != inputVal && undefined != inputVal && inputVal != '' && inputVal != "undefined") {
                address.push(inputVal);
            }
        }

        /* Add country bias*/
		 if (address.length > 0) {

            if (jQuery('#' + geoAddressObj.country).length == 0)
			{
                if (undefined != jr_country_def && jr_country_def != '') {
                    address.push(jr_country_def);
                }

			}
			else {

			inputVal = geomaps._getInputFieldValue(geoAddressObj.country, parentForm);

				if (null != inputVal && undefined != inputVal && inputVal != '' && inputVal != "undefined") {
                    address.push(inputVal);
                }
            }

			address = address.join(' ');

			if (null == geomaps.geocoder) {

				geomaps.geocoder = new google.maps.Geocoder();

			}

			var request = {address: address};

			geomaps.geocoder.geocode(request, function (results, status) {

				if (google.maps.GeocoderStatus.OK == status) {

					var point = results[0].geometry.location;

					geomaps.mapAddLatLonFields(parentForm, point.lat(), point.lng());
				}

				if (callback) {

					callback.call(element, e);

				} else {

					parentForm.submit();

				}

			});

        }
		else {

			if (callback) {
                callback.call(element, e);
            } else {
                parentForm.submit();
            }
        }
    },

    initializeAutocomplete: function (element)
	{
        if (!jQuery(element).data('autocomplete_enabled')) {
            var options = {
                types: ['geocode']
            };

			var autocomplete = new google.maps.places.Autocomplete(element, options);

			jQuery(element)
				.attr('placeholder', GeomapsLanguage["enter_location"])
				.keydown(function (e) {
					if (e.keyCode == 13) {
						e.preventDefault();
						return false;
					}})
				.data('autocomplete_enabled', true)
				;
        }
    },

    getAddress: function () {

        var address = [],
			inputVal = '';

        jQuery.each(geoAddressObj, function (index, item) {

            var form_id = jQuery('#gm_listingAddress').length ? 'gm_listingAddress' : 'jr_listingForm';
            var $input = jQuery('#' + item, '#' + form_id);

            try {

                var type = $input.prop('type');

            } catch (err) {

                var type = $input.attr('type');

            }

            switch (type) {

                case 'select-one':

                    var input = jQuery('#' + item + ' option:selected', '#' + form_id);

                    inputVal = input.val() != '' ? jQuery('#' + item + ' option:selected', '#' + form_id).text() : null;

                    break;

                default:

                    inputVal = jQuery('#' + item, '#' + form_id).val();

                    break;

            }

            if (null != inputVal && undefined != inputVal && inputVal != '' && inputVal != "undefined") {

                address.push(inputVal);

            }

        });



        if (jQuery('#' + geoAddressObj.country).length == 0 && undefined != jr_country_def && jr_country_def != '') {

            address.push(jr_country_def);

        }

        return address.join(' ');

    },

    showAddressOnMap: function () {

        var address = geomaps.getAddress();

        if (null == geomaps.geocoder) {

            geomaps.geocoder = new google.maps.Geocoder();

        }

        var request = {address: address};

        geomaps.geocoder.geocode(request, function (results, status) {

            if (google.maps.GeocoderStatus.OK != status) {

                jQuery('#gm_popupMsg').css({'color': 'red'}).html(GeomapsLanguage["cannot_geocode"]).show();

            } else {

                var point = results[0].geometry.location;

                geomaps.coordinates = [point.lat(), point.lng()];

                geomaps.initializeMap(geomaps.coordinates[0], geomaps.coordinates[1]);

                geomaps.map.setCenter(point); // ?

                geomaps.map.setZoom(15); // ?

                geomaps.marker.setPosition(point);

                if (null == geomaps.infoWindow) {

                    geomaps.infoWindow = new google.maps.InfoWindow();

                }

                geomaps.infoWindow.setContent(address);

                geomaps.infoWindow.open(geomaps.map, geomaps.marker);

                jQuery('#' + jr_lat).val(geomaps.marker.getPosition().lat());

                jQuery('#' + jr_lon).val(geomaps.marker.getPosition().lng());

            }

        });

    },

    /* Popup includes address and lat/lon fields */

    mapPopupFull: function (controller, options, title, lat, lon) {

        var defaults = {

            'listing_id': null,

            'criteria_id': null

        }

        var params = jQuery.extend(defaults, options);


        geomaps.dialog(

            controller,

            '_geocodePopup',

            '&task=single&listing_id=' + params.listing_id + '&criteria_id=' + params.criteria_id,

            {'title': title, 'open': function (event, ui) {

				// Delay required to let ajax finish loading the custom fields
				setTimeout(function() {

					if (lat != '' && lon != '' && lat != 0 && lon != 0) {

						geomaps.initializeMap(lat, lon);

					} else {

						geomaps.showAddressOnMap();

					}

				},300);

            }

            });

    },

    /* Popup only allows marker dragging for fine-tuning of location */

    mapPopupSimple: function () {

        jQuery('#form_container').append('<div style="display:none;" id="gmDelayDiv"></div>'); // Delay trick

        // Clear current lon, lat

        if (jQuery('#' + jr_lat).length > 0) {

            // something

        } else {

            jQuery('#jr_newFields').after(

                '<input type="hidden" id="' + jr_lat + '" name="data[Field][Listing][' + jr_lat + ']" >' +

                '<input type="hidden" id="' + jr_lon + '" name="data[Field][Listing][' + jr_lon + ']" >'

            );

        }

        var lat = jQuery('#' + jr_lat).val();

        var lon = jQuery('#' + jr_lon).val();

        if (lat == '' && lon == '') {

            geomaps.geocodeAddress();

        } else {

            geomaps.mapPopupSimpleDialog(lat, lon);

        }

    },

    mapPopupSimpleDialog: function (lat, lon) {

        var dialog_id = 'jr_formDialog';

        var settings = {

            'modal': false, /* otherwise the marker cannot be dragged with jQuery UI 1.8.5*/

            'autoOpen': true,

            'buttons': function () { },

            'width': '600px',

            'height': 'auto',

            'title': '',

            'open': function () {

				// Delay required to let ajax finish loading the custom fields
				setTimeout(function() {

					geomaps.initializeMap(lat, lon);

				}, 300);

			}

        };

        jQuery('.dialog').dialog('destroy').remove();

        jQuery("body").append('<div id="' + dialog_id + '" class="dialog">'

            + '<div class="ui-widget" style="font-size:11px;margin-bottom: 5px;">'

            + '<div style="padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all">'

            + '<p><span style="float:left;margin-right:0.3em;" class="ui-icon ui-icon-info"></span><strong>' + GeomapsLanguage["drag_marker"] + '</strong></p>'

            + '</div></div><div id="gm_mapPopupCanvas" class="jrMapLoading" style="width: 580px; height: 300px"></div></div>');

        jQuery('#' + dialog_id).dialog(settings);

    },

    geocodeAddress: function () {

        // geocode first

        var address = geomaps.getAddress();

        geomaps.geocoder = new google.maps.Geocoder();

        var request = {address: address};

        geomaps.geocoder.geocode(request, function (results, status) {

            if (google.maps.GeocoderStatus.OK != status) {

                s2Alert(GeomapsLanguage["cannot_geocode"]);

            } else {

                var point = results[0].geometry.location;

                center = point.toUrlValue(); // round the lat/lng values to 6 decimal places by default

                geomaps.coordinates = center.split(',');

                jQuery('#' + jr_lat).val(geomaps.coordinates[0]);

                jQuery('#' + jr_lon).val(geomaps.coordinates[1]);

                geomaps.mapPopupSimpleDialog(geomaps.coordinates[0], geomaps.coordinates[1]);

            }

        });

    },

    clearLatLng: function () {

        jQuery('#' + jr_lat).val('');

        jQuery('#' + jr_lon).val('');

    },

    dialog: function (controller, action, params, options) {

        var dialog_id = 'jr_formDialog';


        var defaults = {

            'modal': false, /* otherwise the marker cannot be dragged with jQuery UI 1.8.5*/

            'autoOpen': true,

            'buttons': function () { },

            'width': '600px',

            'height': 'auto'

        };


        var settings = jQuery.extend(defaults, options);

        jQuery('.dialog').dialog('destroy').remove();

        jQuery("body").append('<div id="' + dialog_id + '" class="dialog"></div>');

        jQuery('#' + dialog_id).load
        (
            s2AjaxUri + '&url=' + controller + '/' + action + '&' + params,

            function () {

                jQuery(this).dialog(settings);

            }

        );

    }

};
