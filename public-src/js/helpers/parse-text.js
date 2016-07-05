'use strict';

/**
 * Function parsed plain text and return html with replaced entries
 * @param text
 * @returns {string}
 */
window.parseItemText = function( text ) {
	// split text to words by space
	var words = text.split(' ');

	// loop all words and try find entries
	for ( var i in words ) {
		// replace urls to html <a></a> tag
		words[i] = words[i].replace(/(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#/%=~_|$?!:,.]*\)|[-A-Z0-9+&@#/%=~_|$?!:,.])*(?:\([-A-Z0-9+&@#/%=~_|$?!:,.]*\)|[A-Z0-9+&@#/%=~_|$])/igm, function(url, isHttpDetected ){
			return '<a target="_blank" href="'+url+'">'+url+'</a>';
		});
	}

	return words.join(' ');
};