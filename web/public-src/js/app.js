'use strict';

$(function(){
	/**
	 * Add resize handler for resize iframe in jira
	 */
	$(window).resize(function(){
		var $app = $('#checklists');
		AP.resize($app.width(), $app.height());
	});

});


var angularApplication = angular.module('checklistsApp', ['ui.sortable', 'ngSanitize']).config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[[').endSymbol(']]');
});