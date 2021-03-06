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
				thisMapData = window[thisMapId],
				displayMode;

			if ($(this).parents('.ghc-map-container').hasClass('display-side')) {
				displayMode = 'side';
			} else if ($(this).hasClass('display-infoWindow')) {
				displayMode = 'infoWindow';
			}

			/**
			 * Set up map data.
			 *
			 * @return {void} Sets up map.
			 */
			if ('object' === typeof thisMapData) {

				var thisPin,
					thisIcon,
					marker,
					infoWindow = new google.maps.InfoWindow({}),
					infoWindowContent,
					LatLngList = [],
					bounds = new google.maps.LatLngBounds(),
					map,
					mapStyles = [];

				/**
				 * Set styles.
				 */
				if ('plain' === thisMapData.style) {
					mapStyles = [
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
					];
				}

				/**
				 * Create map.
				 */
				map = new google.maps.Map($('#' + thisMapId).get(0), {
					center: {
						lat: 35,
						lng: -80
					},
					zoom: 6,
					scrollwheel: false,
					disableDefaultUI: true,
					styles: mapStyles,
				});


				/**
				 * Add pins.
				 */
				for (var key in thisMapData.points) {
					if ({}.hasOwnProperty.call(thisMapData.points, key)) {
						thisPin = thisMapData.points[key];

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
						google.maps.event.addListener(marker, 'click', (function(marker, key) { // eslint-disable-line no-loop-func, no-shadow
							return function() {

								// Side mode.
								if ('side' === displayMode) {
									$('.map-info:visible:not(:only-child)').fadeOut('slow', function() {
										$('.map-info.key-' + key).fadeIn();
									});

									if (window.innerWidth <= 600) {
										$('html, body').animate({
											scrollTop: $('.map-info:visible').offset().top
										}, 750);
									}
								} else if ('infoWindow' === displayMode) {

									// Title.
									infoWindowContent = $('.map-info.key-' + key).html();

									// Display infoWindow.
									infoWindow.setContent(infoWindowContent);
									infoWindow.open(map, marker);
								}
							};
						}(marker, key)));
					}
				}

				/**
				 * Zoom to fit all markers inside map.
				 */
				if (1 < LatLngList.length) {
					for (var j in LatLngList) {
						if ({}.hasOwnProperty.call(LatLngList, j)) {
							bounds.extend(LatLngList[j]);
						}
					}
					map.fitBounds(bounds, 25);
				} else {
					map.setCenter(LatLngList[0]);
					map.setZoom(4);
				}
			}
		});
	});
}(jQuery));
