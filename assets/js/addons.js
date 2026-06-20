/**
 * Storefront add-on fields — presentation only.
 *
 * The single moment: when a customer commits a personalisation (types an
 * engraving, picks an option), the field signs itself off with an ink
 * underline. This script only toggles a class that drives that CSS state —
 * it changes no values, names, or submission behaviour.
 */
(function () {
	'use strict';

	var INSCRIBED = 'addons-field--inscribed';

	/**
	 * A control counts as "inscribed" once it holds a committed value:
	 * text with non-whitespace content, or a select past its placeholder.
	 *
	 * @param {HTMLElement} control
	 * @return {boolean}
	 */
	function isInscribed(control) {
		if (control.tagName === 'SELECT') {
			return control.value !== '';
		}

		return control.value.trim() !== '';
	}

	/**
	 * @param {HTMLInputElement} control
	 */
	function updateCharCounter(control) {
		var field = control.closest('.addons-field');
		if (!field) {
			return;
		}

		var counter = field.querySelector('[data-addons-char-counter]');
		if (!counter) {
			return;
		}

		var min = parseInt(counter.getAttribute('data-min') || '0', 10);
		var max = parseInt(counter.getAttribute('data-max') || '0', 10);
		var ignoreSpaces = counter.getAttribute('data-ignore-spaces') === '1';

		var val = control.value || '';
		if (ignoreSpaces) {
			val = val.replace(/\s+/g, '');
		}

		var length = val.length;
		var label = '';

		if (min > 0 && max > 0) {
			label = length + ' / ' + min + '-' + max;
		} else if (max > 0) {
			label = length + ' / ' + max;
		} else if (min > 0) {
			label = length + ' / min ' + min;
		}

		counter.textContent = label;

		var invalid = (min > 0 && length < min) || (max > 0 && length > max);
		counter.classList.toggle('addons-char-counter--invalid', invalid);
	}

	/**
	 * @param {HTMLElement} control
	 */
	function reflect(control) {
		var field = control.closest('.addons-field');

		if (!field) {
			return;
		}

		field.classList.toggle(INSCRIBED, isInscribed(control));

		if (control.tagName === 'INPUT' && control.type === 'text') {
			updateCharCounter(control);
		}
	}

	function init() {
		var controls = document.querySelectorAll(
			'.addons-field--text .input-text, .addons-field--select select'
		);

		Array.prototype.forEach.call(controls, function (control) {
			// Honour prefilled / restored values (e.g. validation reload).
			reflect(control);

			control.addEventListener('input', function () {
				reflect(control);
			});
			control.addEventListener('change', function () {
				reflect(control);
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
