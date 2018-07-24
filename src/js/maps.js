/**
 * GHC Map
 *
 * @author  AndrewRMinion Design
 * @package GHC_Functionality
 */
/* global google, jQuery, window */
'use strict';
(function($) {
	$('document').ready(function() {

		/**
		 * Loop over all maps.
		 */
		$('.ghc-map').each(function() {
			var thisMapId = $(this).attr('id'),
				thisMapData = window[thisMapId];

			/**
			 * Set up map data.
			 *
			 * @return {void} Sets up map.
			 */
			if ('object' === typeof thisMapData) {
				var thisPin,
					thisIcon,
					marker,
					LatLngList = [],
					bounds = new google.maps.LatLngBounds(),
					map = new google.maps.Map($('#' + thisMapId).get(0), {
						center: {
							lat: 35,
							lng: -80
						},
						zoom: 6,
						scrollwheel: false,
						disableDefaultUI: true,
						styles: [
							{
								elementType: 'geometry',
								stylers: [{
									color: '#dbdbdb'
								}]
							}, {
								elementType: 'labels',
								stylers: [{
									visibility: 'off'
								}]
							}, {
								featureType: 'administrative',
								stylers: [{
									visibility: 'off'
								}]
							}, {
								featureType: 'poi',
								stylers: [{
									visibility: 'off'
								}]
							}, {
								featureType: 'road',
								stylers: [{
									visibility: 'off'
								}]
							}, {
								featureType: 'transit',
								stylers: [{
									visibility: 'off'
								}]
							}, {
								featureType: 'water',
								elementType: 'geometry',
								stylers: [{
									color: '#ffffff'
								}]
							}
						]
					});

				/**
				 * Add pins
				 */
				for (var key in thisMapData) {
					thisPin = thisMapData[key];

					thisIcon = {
						url: thisPin.icon,
						scaledSize: new google.maps.Size(50, 50),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(25, 25)
					};

					// Add marker pin.
					marker = new google.maps.Marker({
						position: new google.maps.LatLng(thisPin.address.map.lat, thisPin.address.map.lng),
						map: map,
						icon: thisIcon,
						title: thisPin.title,
					});

					// Add pin to LatLng list for fit-to-bounds.
					LatLngList.push(new google.maps.LatLng(thisPin.address.map.lat, thisPin.address.map.lng));

					// Add infoWindow listener and content.
					google.maps.event.addListener(marker, 'click', (function(marker, key) {
						return function() {
							$('.map-info:visible').fadeOut('slow', function() {
								$('.map-info#' + key).fadeIn();
							});
						}
					}(marker, key)));
				}

				/**
				 * Zoom to fit all markers inside map.
				 */
				if (1 < LatLngList.length) {
					for (var j in LatLngList) {
						bounds.extend(LatLngList[j]);
					}
					map.fitBounds(bounds, 25);
				} else {
					map.setCenter(LatLngList[0]);
					map.setZoom(8);
				}
			}
		});
	});
}(jQuery));
