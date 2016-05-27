'use strict';

$(function(){
	/**
	 * Add resize handler for resize iframe in jira
	 */
	var appWidth = false;
	$(window).resize(function(){
		var $app = $('#checklists');
		if ( ! appWidth ) {
			appWidth = $app.width();
		}
		console.log('Set width: ' + appWidth);
		AP.resize(appWidth, $app.height());
	});

});


var angularApplication = angular.module('checklistsApp', ['ui.sortable', 'ngSanitize', 'colorpicker.module']).config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[[').endSymbol(']]');
});