'use strict';

var checklistsController = angularApplication.controller('ChecklistsController', ["$scope", "$sce", function($scope, $sce) {
	var checklistsCtrl = this;
	var appData = window.checklistsData;

	/**
	 * Initialized JWT token 
	 * @param location
	 * @returns {{}}
	 */
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
	var getEndpointWithToken = function( endpoint ) {
		return endpoint + '?jwt=' + jwtToken;
	};
	var jwtToken = parseLocation(window.location.search)['jwt'];

	/**
	 * Initialize error occurred flag
	 * @type {boolean}
	 */
	checklistsCtrl.errorOccurred = false;

	var parseItemText = function( text ) {
		var words = text.split(' ');
		for ( var i in words ) {
			words[i] = words[i].replace(/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/ig, '<a target="_blank" href="$&">$&</a>');
		}
		
		return words.join(' ');
	};
	
	/**
	 * Initialized checklists model
	 */
	checklistsCtrl.checklists = appData.checklists;
	
	for ( var i in checklistsCtrl.checklists ) {
		checklistsCtrl.checklists[i].editName = checklistsCtrl.checklists[i].name;
		
		for ( var j in checklistsCtrl.checklists[i].items ) {
			checklistsCtrl.checklists[i].items[j].editText = checklistsCtrl.checklists[i].items[j].text;
			checklistsCtrl.checklists[i].items[j].parsedText = $sce.trustAsHtml( parseItemText( checklistsCtrl.checklists[i].items[j].text ) );
		}
	}

	/**
	 * Initialized new checklist model
	 * @type {{name: string}}
	 */
	checklistsCtrl.newChecklist = {
		'name': '',
		'editMode': false
	};

	$scope.checklistsSortableOptions = {
		handle: '.checklist-wapper i.cl-icon-notebook-list',
		stop: function(e) {
			var ids = [];
			for (var i in checklistsCtrl.checklists ) {
				ids.push( checklistsCtrl.checklists[i].id );
			}
			
			jQuery.post( getEndpointWithToken( appData.endpoints.order_checklist ), {
				'issue_id': appData.issue_id,
				'orders': ids 
			}, function( response ){
				if (response.status != true) {
					checklistsCtrl.errorOccurred = true;
				}
			}).fail(function() {
				checklistsCtrl.errorOccurred = true;
				$scope.$apply();
			});
		}
	};

	$scope.itemsSortableOptions = {
		items: "li:not(.add-new-item-link)",
		connectWith: "ul.checklist",
		stop: function(e) {
			var checklistsSorts = {};
			for (var i in checklistsCtrl.checklists ) {
				var itemIds = [];
				for (var j in checklistsCtrl.checklists[i].items) {
					itemIds.push( checklistsCtrl.checklists[i].items[j].id );
				}
				checklistsSorts[ checklistsCtrl.checklists[i].id ] = itemIds;
			}
			
			//console.log(checklistsSorts);
			jQuery.post( getEndpointWithToken( appData.endpoints.order_items ), {
				'issue_id': appData.issue_id,
				'checklists_sort': checklistsSorts
			}, function( response ){
				if (response.status != true) {
					checklistsCtrl.errorOccurred = true;
				}
			}).fail(function() {
				checklistsCtrl.errorOccurred = true;
				$scope.$apply();
			});
		}
	};

	/**
	 * Create new checklist
	 */
	checklistsCtrl.addNewChecklist = function() {
		if ( ! checklistsCtrl.newChecklist.name ) {
			return;
		}
		
		jQuery.post( getEndpointWithToken( appData.endpoints.create_checklist ), {
			'issue_id': appData.issue_id,
			'name': checklistsCtrl.newChecklist.name
		}, function( response ) {
			if (response.status != true) {
				checklistsCtrl.errorOccurred = true;
				return;
			}

			checklistsCtrl.checklists.push({
				'id': response.checklist_id,
				'name': checklistsCtrl.newChecklist.name,
				'editName': checklistsCtrl.newChecklist.name,
				'items': [],
				'completedPercents': "0%"
			});

			console.log('Created new checklist "'+checklistsCtrl.newChecklist.name+'" with ID: ' + response.checklist_id);

			checklistsCtrl.newChecklist.name = '';
			checklistsCtrl.newChecklist.editMode = false;

			$scope.$apply();
		}).fail(function() {
			checklistsCtrl.errorOccurred = true;
			$scope.$apply();
		});
	};

	/**
	 * Update checklist name
	 * @param checklist
	 */
	checklistsCtrl.updateChecklist = function( checklist ) {
		if ( checklist.name == checklist.editName ) {
			checklist.editMode = ! checklist.editMode;
			return;
		}
		
		checklist.name = checklist.editName;
		jQuery.post( getEndpointWithToken( appData.endpoints.update_checklist ), {
			'issue_id': appData.issue_id,
			'id': checklist.id,
			'name': checklist.name
		}, function( response ) {
			if (response.status != true) {
				checklistsCtrl.errorOccurred = true;
				return;
			}

			console.log('New checklist name: ' + checklist.name);
			checklist.editMode = ! checklist.editMode;

			$scope.$apply();
		}).fail(function() {
			checklistsCtrl.errorOccurred = true;
			$scope.$apply();
		});
	};

	/**
	 * Remove checklist
	 * @param checklist
	 */
	checklistsCtrl.removeChecklist = function( checklist ) {
		for ( var i in checklistsCtrl.checklists ) {
			if (checklistsCtrl.checklists[i].id == checklist.id) {
				jQuery.post( getEndpointWithToken( appData.endpoints.remove_checklist ), {
					'issue_id': appData.issue_id,
					'id': checklist.id
				}, function( response ) {
					if (response.status != true) {
						checklistsCtrl.errorOccurred = true;
					}

					console.log('Removed checklist "'+checklist.name+'" with ID: ' + checklist.id);
				}).fail(function() {
					checklistsCtrl.errorOccurred = true;
					$scope.$apply();
				});
				checklistsCtrl.checklists.splice(i,1);
			}
		}
	};

	/**
	 * Add new item to checklist
	 * @param checklist
	 */
	checklistsCtrl.addNewItem = function( checklist ) {
		if ( ! checklist.newItemText ) {
			return;
		}
		
		jQuery.post( getEndpointWithToken( appData.endpoints.create_item ), {
			'issue_id': appData.issue_id,
			'checklist_id': checklist.id,
			'item_text': checklist.newItemText
		}, function( response ) {
			if (response.status != true) {
				checklistsCtrl.errorOccurred = true;
				return;
			}

			checklist.items.push({
				'id': response.item_id,
				checked: false,
				'text': checklist.newItemText,
				'editText': checklist.newItemText
			});
			checklist.completedPercents = getCompletedPercents( checklist.items );

			console.log('Created new item "'+checklist.newItemText+'" with ID: ' + response.item_id);

			checklist.newItemText = '';

			$scope.$apply();
		}).fail(function() {
			checklistsCtrl.errorOccurred = true;
			$scope.$apply();
		});
	};

	/**
	 * update item
	 * @param item
	 */
	checklistsCtrl.updateItem = function( checklist, item ) {
		if ( item.editText == item.text ) {
			item.editMode = ! item.editMode;
			return;
		}

		item.text = item.editText;
		
		jQuery.post( getEndpointWithToken( appData.endpoints.update_item ), {
			'issue_id': appData.issue_id,
			'checklist_id': checklist.id,
			'item_id': item.id,
			'item_text': item.text
		}, function( response ) {
			if (response.status != true) {
				checklistsCtrl.errorOccurred = true;
				return;
			}

			console.log('Updated item "'+item.text+'" with ID: ' + item.id);
			item.editMode = ! item.editMode;

			$scope.$apply();
		}).fail(function() {
			checklistsCtrl.errorOccurred = true;
			$scope.$apply();
		});
	};

	/**
	 * Remove item from checklist
	 * @param checklist
	 * @param item
	 */
	checklistsCtrl.removeItem = function( checklist, item ) {
		for ( var i in checklist.items ) {
			if ( checklist.items[i].id == item.id ) {
				jQuery.post( getEndpointWithToken( appData.endpoints.remove_item ), {
					'issue_id': appData.issue_id,
					'checklist_id': checklist.id,
					'item_id': item.id
				}, function( response ) {
					if (response.status != true) {
						checklistsCtrl.errorOccurred = true;
					}

					console.log('Removed item "'+item.text+'" with ID: ' + item.id + ' in checklist "'+checklist.name+'" with ID: ' + checklist.id);
				}).fail(function() {
					checklistsCtrl.errorOccurred = true;
					$scope.$apply();
				});
				checklist.items.splice(i,1);
				checklist.completedPercents = getCompletedPercents( checklist.items );
			}
		}
	};

	/**
	 * Complete/Un-complete item in checklist
	 * @param el
	 */
	checklistsCtrl.completeItem = function(checklist, item) {
		item.checked = !item.checked;
		checklist.completedPercents = getCompletedPercents( checklist.items );
		jQuery.post( getEndpointWithToken( appData.endpoints.complete_item ), {
			'issue_id': appData.issue_id,
			'checklist_id': checklist.id,
			'item_id': item.id
		}, function( response ) {
			if (response.status != true) {
				checklistsCtrl.errorOccurred = true;
			}

			if ( response.checked ) {
				console.log('Completed item "'+item.text+'" with ID: ' + item.id);
			} else {
				console.log('Uncompleted item "'+item.text+'" with ID: ' + item.id);
			}
		}).fail(function() {
			checklistsCtrl.errorOccurred = true;
			$scope.$apply();
		});
	};

	/**
	 * Toggle new checklist form
	 */
	checklistsCtrl.toggleNewChecklistForm = function() {
		checklistsCtrl.newChecklist.editMode = ! checklistsCtrl.newChecklist.editMode;
	};

	/**
	 * Toggle checklist in/out edit mode
	 * @param checklist
	 */
	checklistsCtrl.toggleChecklistEdit = function( checklist ) {
		checklist.editMode = ! checklist.editMode;
		
		if ( checklist.editMode && ! checklist.editName ) {
			checklist.editName = checklist.name;
		} 
	};
	
	checklistsCtrl.toggleNeItemEditMode = function( checklist ) {
		checklist.newItemEditMode = ! checklist.newItemEditMode;
	};

	/**
	 * Toggle item edit mode
	 * @param item
	 */
	checklistsCtrl.toggleItemEditMode = function( item ) {
		item.editMode = ! item.editMode;
		
		if ( item.editMode && ! item.editText ) {
			item.editText = item.text;
		}
	};

	/**
	 * Function return completed percents items in checklist
	 * @param items
	 * @returns {string}
	 */
	var getCompletedPercents = function(items) {
		var checked = 0;
		for (var i in items) {
			if (items[i].checked) {
				checked++;
			}
		}
		
		if ( ! items.length ) {
			return '0%';
		}
		return Math.round( (checked * 100) / items.length  ) + '%';
	};

	/**
	 * Loop all checklists and init completed percents
	 */
	for (var i in checklistsCtrl.checklists) {
		checklistsCtrl.checklists[i].completedPercents = getCompletedPercents( checklistsCtrl.checklists[i].items );
	}
	
}]);