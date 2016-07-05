/**
 * Function return completed percents items in checklist
 * @param items
 * @returns {string}
 */
window.getCompletedPercents = function(items) {
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