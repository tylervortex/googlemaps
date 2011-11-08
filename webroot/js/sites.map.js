var map = null;
var geocoder = null;
var marker = null;
function setLatLng(lat,lng) {
	document.getElementById("SiteLatitude").value = lat;
	document.getElementById("SiteLongitude").value = lng;
	document.getElementById("SiteLatLng").style.display = "none";
}
function initialize() {
	if (google.maps.BrowserIsCompatible()) {
		map = new google.maps.Map2(document.getElementById("map_canvas"));
		map.setUIToDefault();
		geocoder = new google.maps.ClientGeocoder();
		if (document.getElementById("SiteLatitude").value && document.getElementById("SiteLongitude").value) {
			document.getElementById("SiteLatLng").style.display = "none";
			setMarker(new google.maps.LatLng(document.getElementById("SiteLatitude").value, document.getElementById("SiteLongitude").value));
			var point = new google.maps.LatLng(document.getElementById("SiteLatitude").value, document.getElementById("SiteLongitude").value);
			clicked(null, point, 0);
		} else {
			document.getElementById("SiteLatLng").style.display = "block";
			setMarker(new google.maps.LatLng(-25.440136, -49.273725));
		}
	}
}
function setMarker(point) {
	map.setCenter(point, 13);
	if (!marker) {
		marker = new google.maps.Marker(point, { draggable: true });
	} else {
		marker.setLatLng(point);
	}
	google.maps.Event.addListener(marker, "dragstart", function() {
		map.closeInfoWindow();
	});
	google.maps.Event.addListener(marker, "dragend", function(latlng) {
		setLatLng(latlng.lat(),latlng.lng());
		clicked(null, latlng);
	});
	google.maps.Event.addListener(marker, "click", function(latlng) {
		clicked(null, latlng);
	});
	map.addOverlay(marker);
}
function showAddress() {
	var address = document.getElementById("SiteAddress").value;
	geocoder.getLatLng(
		address,
		function(point) {
			if (!point) {
				document.getElementById("SiteLatLng").style.display = "block";
				alert(address + " não encontrado");
			} else {
				setMarker(point);
				setLatLng(point.lat(), point.lng());
				marker.openInfoWindowHtml(address);
			}
		}
	);
}
function showLatLng() {
	var point = new google.maps.LatLng(
		document.getElementById("SiteLatitude").value,
		document.getElementById("SiteLongitude").value
	);
	setMarker(point);
	setLatLng(point.lat(), point.lng());
	clicked(null, point);
}
function showUtm() {
	coordsConvert();
	var point = new google.maps.LatLng(
		document.getElementById("SiteLatitude").value,
		document.getElementById("SiteLongitude").value
	);
	setMarker(point);
	setLatLng(point.lat(), point.lng());
	clicked(null, point);
}

function clicked(overlay, latlng, city) {
	if (latlng) {
		geocoder.getLocations(latlng, function(addresses) {
			if (addresses.Status.code != 200) {
				alert("reverse geocoder failed to find an address for " + latlng.toUrlValue());
			} else {
				address = addresses.Placemark[0];
				var myHtml = address.address;
				map.openInfoWindow(latlng, myHtml);
				document.getElementById("SiteAddress").value = address.address;
				document.getElementById("SiteState").value = address.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName;
				if (city != 0) {
					if (address.AddressDetails.Country.AdministrativeArea.Locality
					    && address.AddressDetails.Country.AdministrativeArea.Locality.LocalityName) {
						document.getElementById("SiteCity").value = address.AddressDetails.Country.AdministrativeArea.Locality.LocalityName;
					} else {
						document.getElementById("SiteCity").value = '';
					}
				}
			}
		});
	}
}
google.load("maps", "2", { "language": "pt-BR" });
google.setOnLoadCallback(initialize);
$(document).ready(function () {
	$("#SiteAddress").keypress(function(e) {
		if (e.keyCode == "13") {
			e.preventDefault();
			showAddress();
		}
		$("#SiteAddressTypeAddress").attr("checked", "checked");
	});
	$("#SiteAddress").focus(function(e) {
		$("#SiteAddressTypeAddress").attr("checked", "checked");
	});
	$("#SiteUtm").keypress(function(e) {
		if (e.keyCode == "13") {
			e.preventDefault();
			showUtm();
		}
		$("#SiteAddressTypeUtm").attr("checked", "checked");
	});
	$("#SiteUtm").focus(function(e) {
		$("#SiteAddressTypeUtm").attr("checked", "checked");
	});
	$("#SiteLatitude").keypress(function(e) {
		if (e.keyCode == "13") {
			e.preventDefault();
		}
		$("#SiteAddressTypeLatlng").attr("checked", "checked");
	});
	$("#SiteLatitude").focus(function(e) {
		$("#SiteAddressTypeLatlng").attr("checked", "checked");
	});
	$("#SiteLongitude").keypress(function(e) {
		if (e.keyCode == "13") {
			e.preventDefault();
		}
		$("#SiteAddressTypeLatlng").attr("checked", "checked");
	});
	$("#SiteLongitude").focus(function(e) {
		$("#SiteAddressTypeLatlng").attr("checked", "checked");
	});
	$("#SiteAddressSearch").click(function() {
		type = $("input[name='data\\[Site\\]\\[address_type\\]']:checked").val();
		if (type == "latlng") {
			showLatLng();
		} else if (type == "utm") {
			showUtm();
		} else {
			showAddress();
		}
	});
});

/* Conversão de coordenadas no formato
 *
 * S 26° 40' 24" W 51° 43' 1"
 *
 * para o formato
 *
 * -26.673333333333336,51.716944444444444
 *
 * Código adaptado de: http://pages.globetrotter.net/roule/utmgoogle.htm
 */
var deg2rad = Math.PI / 180;

function parseCoordsCheck(parts) {
	if (parts != null) {
		for (var i = 0; i < parts.length; i++) {
			if (parts[i] == undefined) {
				return 0;
			}
		}
		return 1;
	}
	return 0;
}
function parseCoordsSwap(parts) {
	var x;
	x = parts[4];
	y = parts[8];
	for (i = 4; i > 1; i--) {
		parts[i] = parts[i - 1];
		parts[i + 4] = parts[i + 4 - 1];
	}
	parts[1] = x;
	parts[5] = y;
	return parts;
}

function parseCoordsAdjust(parts) {
	if ((parts[1] == 'S' || parts[1] == 'N') && (parts[5] != 'W' && parts[5] != 'E')) {
		return null;
	} else {
		if (parts[1] == 'W' || parts[1] == 'E') {
			var x;
			for (var i = 1; i <= 4; i++) {
				x = parts[i + 4];
				parts[i] = parts[i + 4];
				parts[i + 4] = x;
			}
		}
		if (parts[1] == 'N') {
			parts[1] = 1;
		} else {
			parts[1] = -1;
		}
		if (parts[5] == 'E') {
			parts[5] = 1;
		} else {
			parts[5] = -1;
		}
		return parts;
	}
}

function parseCoords(str) {
	var parts;
	/* Testar S 26°40'24.00" W 51°43'1.00" */
	parts = str.match(/[^SNWE]*([SNWE])?[^0-9\.\-]*([0-9\.\-]+)?[^0-9\.]*([0-9\.]+)?[^0-9\.]*([0-9\.]+)?[^SNWE]*([SNWE])?[^0-9\.]*([0-9\.\-]+)[^0-9\.]*([0-9\.]+)?[^0-9\.]*([0-9\.]+)?/);
	if (parseCoordsCheck(parts)) {
		return parseCoordsAdjust(parts);
	}
	/* Testar 26°40'24.00" S 51°43'1.00" W */
	parts = str.match(/[^0-9\.\-]*([0-9\.\-]+)?[^0-9\.]*([0-9\.]+)?[^0-9\.]*([0-9\.]+)?[^SNWE]*([SNWE])?[^0-9\.]*([0-9\.\-]+)[^0-9\.]*([0-9\.]+)?[^0-9\.]*([0-9\.]+)?[^SNWE]*([SNWE])?/);
	if (parseCoordsCheck(parts)) {
		parts = parseCoordsSwap(parts);
		return parseCoordsAdjust(parts);
	}
	return null;
}

function validateDms(latd, latm, lats, latb, lonm, lond, lons, lonb) {
	if (Math.abs(Number(latd)) >= 90)
		return 0;
	if (Number(latm) > 60)
		return 0;
	if (Number(lats) > 60)
		return 0;
	if (Math.abs(Number(lond)) >= 180)
		return 0;
	if (Number(lonm) > 60)
		return 0;
	if (Number(lons) > 60)
		return 0;
	return 1;
}

function toDms(lat, lon) {
	var latbrg = 'N';
	var lonbrg = 'E';
	if (lat < 0)
		latbrg = 'S';
	if (lon < 0)
		lonbrg = 'W';
	// LEW: have to round here, else could end up with 60 seconds :-)
	var tlat = Math.abs(lat) + 0.5 / 360000;  // round up 0.005 seconds (1/100th)
	var tlon = Math.abs(lon) + 0.5 / 360000;
	var tlatdm = Math.abs(lat) + 0.5 / 60000;  // round up 0.0005 minutes (1/1000th)
	var tlondm = Math.abs(lon) + 0.5 / 60000;
	var deglat = Math.floor(tlat);
	var t = (tlat - deglat) * 60;
	var minlat = Math.floor(t);
	var minlatdm = Math.floor((tlatdm - Math.floor(tlatdm)) * 60 * 1000) / 1000;
	var seclat = (t - minlat) * 60;
	seclat = Math.floor(seclat * 100) / 100;  //  works in js 1.4
	// seclat = seclat.toFixed(2);  // 2 decimal places js 1.5 and later
	var deglon = Math.floor(tlon);
	t = (tlon - deglon) * 60;
	var minlon = Math.floor(t);
	var minlondm = Math.floor((tlondm - Math.floor(tlondm)) * 60 * 1000) / 1000;
	var seclon = (t - minlon) * 60;
	seclon = Math.floor(seclon * 100) / 100;  // js 1.4
	// seclon = seclon.toFixed(2);  // js 1.5 and later
	return latbrg + " " + deglat + "\u00B0 " + minlat + "' " + seclat + "\" " + lonbrg + " " + deglon + "\u00B0 " + minlon + "' " + seclon + "\"";
}

function coordsConvert() {
	var parts = parseCoords($("#SiteUtm").val());
	var latb = parts[1];
	var latd = parts[2];
	var latm = parts[3];
	var lats = parts[4];
	var lonb = parts[5];
	var lond = parts[6];
	var lonm = parts[7];
	var lons = parts[8];
	if (!validateDms(latd, latm, lats, latb, lonm, lond, lons, lonb)) {
		alert("Valor inválido");
		return;
	}
	var lat = (Number(latd) + Number(latm) / 60 + Number(lats) / 3600) * latb;
	var lon = (Number(lond) + Number(lonm) / 60 + Number(lons) / 3600) * lonb;
	var str = toDms(lat, lon);
	setLatLng(lat, lon);
	$("#SiteUtm").val(str);
}
