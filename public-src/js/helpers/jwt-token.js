'use strict';

window.getEndpointWithToken = function( endpoint ) {
	var parseLocation = function(location) {
		var pairs = location.substring(1).split("&");
		var obj = {};
		var pair;
		var i;

		for ( i in pairs ) {
			if ( pairs[i] === "" ) continue;

			pair = pairs[i].split("=");
			obj[ decodeURIComponent( pair[0] ) ] = decodeURIComponent( pair[1] );
		}

		return obj;
	};
	
	return endpoint + '?jwt=' + parseLocation(window.location.search)['jwt'];
};