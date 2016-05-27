'use strict';

var angularApplication = angular.module('checklistsApp', ['ui.sortable', 'ngSanitize', 'colorpicker.module']).config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[[').endSymbol(']]');
});