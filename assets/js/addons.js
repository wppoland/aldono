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
	 * @param {HTMLElement} control
	 */
	function reflect(control) {
		var field = control.closest('.addons-field');

		if (!field) {
			return;
		}

		field.classList.toggle(INSCRIBED, isInscribed(control));
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
