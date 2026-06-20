/**
 * Add-Ons product-data repeater (vanilla JS, no jQuery).
 *
 * Adds/removes add-on definition rows in the WooCommerce product editor. New
 * rows are cloned from the inline <script> template with a fresh index so the
 * posted field names stay unique. Uses event delegation, keeps an accessible
 * empty state in sync, and moves focus to the first input of a new row.
 */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	ready(function () {
		var panel = document.getElementById('addons_product_data');

		if (!panel) {
			return;
		}

		var rows = panel.querySelector('[data-addons-rows]');
		var addBtn = panel.querySelector('[data-addons-add]');
		var template = document.getElementById('addons-row-template');
		var empty = panel.querySelector('[data-addons-empty]');

		if (!rows || !addBtn || !template) {
			return;
		}

		var nextIndex = rows.querySelectorAll('[data-addons-row]').length;

		function rowCount() {
			return rows.querySelectorAll('[data-addons-row]').length;
		}

		function syncEmptyState() {
			if (empty) {
				empty.hidden = rowCount() > 0;
			}
		}

		function addRow() {
			var html = template.innerHTML.replace(/__INDEX__/g, String(nextIndex));
			nextIndex += 1;

			var wrap = document.createElement('div');
			wrap.innerHTML = html.trim();

			var node = wrap.firstElementChild;

			if (!node) {
				return;
			}

			rows.appendChild(node);
			syncEmptyState();

			var firstInput = node.querySelector('input, select, textarea');

			if (firstInput) {
				firstInput.focus();
			}
		}

		function syncCharSettings(row) {
			var select = row.querySelector('select[name*="[type]"]');
			var charSettings = row.querySelector('[data-addons-char-settings]');

			if (!select || !charSettings) {
				return;
			}

			charSettings.style.display = select.value === 'text' ? '' : 'none';
		}

		function syncAllRows() {
			rows.querySelectorAll('[data-addons-row]').forEach(syncCharSettings);
		}

		addBtn.addEventListener('click', addRow);

		rows.addEventListener('click', function (event) {
			var target = event.target;

			if (!target || !target.closest('[data-addons-remove]')) {
				return;
			}

			var row = target.closest('[data-addons-row]');

			if (row && row.parentNode) {
				row.parentNode.removeChild(row);
				syncEmptyState();
			}
		});

		rows.addEventListener('change', function (event) {
			var target = event.target;

			if (target && target.matches('select[name*="[type]"]')) {
				var row = target.closest('[data-addons-row]');
				if (row) {
					syncCharSettings(row);
				}
			}
		});

		syncEmptyState();
		syncAllRows();
	});
})();
