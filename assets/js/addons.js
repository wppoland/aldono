/**
 * Storefront add-on fields, presentation only: the inscribed state, the
 * character counter and the live subtotal.
 *
 * The single moment: when a customer commits a personalisation (types an
 * engraving, picks an option), the field signs itself off with an ink
 * underline. This script only toggles a class that drives that CSS state, 
 * it changes no values, names, or submission behaviour.
 */
(function () {
	'use strict';

	var INSCRIBED = 'addons-field--inscribed';

	/**
	 * A control counts as "inscribed" once it holds a committed value:
	 * a text, textarea, number or date with non-whitespace content, or a select
	 * past its placeholder.
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

		if (control.tagName === 'TEXTAREA' || (control.tagName === 'INPUT' && control.type === 'text')) {
			updateCharCounter(control);
		}
	}

	/**
	 * The price of one add-on control as the cart will charge it: a field adds
	 * its price once it holds a value, a select or radio adds the price of the
	 * chosen option. Mirrors captureCartItemData() + resolvePrice() in
	 * lib/storefront-kit/AddOns/ProductAddOnsEngine.php, where the server trims
	 * and strips tags (sanitize_text_field) before testing for an empty value.
	 *
	 * @param {HTMLElement} control
	 * @return {number}
	 */
	function controlPrice(control) {
		var source = control;

		if (control.tagName === 'SELECT') {
			source = control.value === '' ? null : control.options[control.selectedIndex];
		} else if (control.type === 'checkbox' || control.type === 'radio') {
			source = control.checked ? control : null;
		} else if (control.value.replace(/<[^>]*>/g, '').trim() === '') {
			source = null;
		}

		return source ? parseFloat(source.getAttribute('data-addons-price') || '0') || 0 : 0;
	}

	/**
	 * The cart line as displayed: (unit price + add-on prices) x quantity, all
	 * raw as entered, times the store's tax display factor. WooCommerce converts
	 * the whole line once, so this converts once too; formatPrice() rounds.
	 *
	 * @param {number}   base   Raw product or variation price.
	 * @param {number[]} prices Raw add-on prices currently selected.
	 * @param {number}   qty
	 * @param {number}   factor Engine::displayFactor(), 1 when taxes change nothing.
	 * @return {number}
	 */
	function lineTotal(base, prices, qty, factor) {
		var unit = base;

		prices.forEach(function (price) {
			unit += price;
		});

		return unit * qty * factor;
	}

	/**
	 * Format like wc_price(): rounded half up to the store decimals the way PHP
	 * round() does (0.615 is 0.61499... in binary, PHP still gives 0.62), grouped
	 * with the store separators, the symbol placed by the store price format.
	 *
	 * @param {number} amount
	 * @param {{decimals: number, decimalSeparator: string, thousandSeparator: string, symbol: string, priceFormat: string}} fmt
	 * @return {string}
	 */
	function formatPrice(amount, fmt) {
		var factor = Math.pow(10, fmt.decimals);
		var parts = (Math.round(Number((amount * factor).toPrecision(15))) / factor).toFixed(fmt.decimals).split('.');
		var number = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, fmt.thousandSeparator)
			+ (parts[1] ? fmt.decimalSeparator + parts[1] : '');

		return fmt.priceFormat.replace('%1$s', fmt.symbol).replace('%2$s', number);
	}

	/**
	 * Wire the live subtotal of one add-on group. Empty while the price is not
	 * known (no variation chosen, quantity cleared); the region keeps its
	 * height either way so the form never shifts.
	 *
	 * @param {HTMLElement} region
	 */
	function initSubtotal(region) {
		var form = region.closest('form');
		var group = region.closest('.addons-fields');

		if (!form || !group) {
			return;
		}

		var base = parseFloat(region.getAttribute('data-base') || '');
		var taxFactor = parseFloat(region.getAttribute('data-tax-factor') || '1');
		var fmt = {
			decimals: parseInt(region.getAttribute('data-decimals') || '2', 10),
			decimalSeparator: region.getAttribute('data-decimal-separator') || '.',
			thousandSeparator: region.getAttribute('data-thousand-separator') || '',
			symbol: region.getAttribute('data-symbol') || '',
			priceFormat: region.getAttribute('data-price-format') || '%1$s%2$s'
		};
		var label = region.getAttribute('data-label') || '%s';

		function update() {
			var qtyInput = form.querySelector('input[name="quantity"]');
			var qty = qtyInput ? parseFloat(qtyInput.value) : 1;
			var prices = Array.prototype.map.call(group.querySelectorAll('[name][data-addons-price], select[name]'), controlPrice);
			var total = lineTotal(base, prices, qty, taxFactor);
			var text = qty > 0 && isFinite(total) && total >= 0 ? label.replace('%s', formatPrice(total, fmt)) : '';

			if (region.textContent !== text) {
				region.textContent = text;
			}
		}

		form.addEventListener('input', update);
		form.addEventListener('change', update);

		// WooCommerce announces variations with jQuery .trigger(), which emits no
		// native DOM event, so this is the one place jQuery is touched. It is
		// already on the page wherever a variable product form is.
		if (window.jQuery) {
			window.jQuery(form)
				.on('found_variation', function (event, variation) {
					base = variation && variation.addons_price !== undefined ? parseFloat(variation.addons_price) : NaN;
					taxFactor = variation && variation.addons_tax_factor !== undefined ? parseFloat(variation.addons_tax_factor) : NaN;
					update();
				})
				.on('reset_data', function () {
					base = NaN;
					update();
				});
		}

		update();
	}

	function init() {
		Array.prototype.forEach.call(document.querySelectorAll('[data-addons-subtotal]'), initSubtotal);

		var controls = document.querySelectorAll(
			'.addons-field .input-text, .addons-field--select select'
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

	// The pure price math, for tests/subtotal-check.js.
	window.addonsSubtotalMath = { lineTotal: lineTotal, formatPrice: formatPrice };

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
