/**
 * The live subtotal must show the number the cart will charge:
 * (unit price + add-on prices) x quantity x tax display factor, formatted like
 * wc_price(). tests/tax-modes.json holds the cart's own line subtotals in all
 * four tax setups and the choice prices the cart and order meta show,
 * produced by tests/tax-display-check.php.
 *
 * Run: node tests/subtotal-check.js
 */
'use strict';

const fs = require('fs');
const path = require('path');
const vm = require('vm');
const assert = require('assert');

const ctx = { window: {}, document: { readyState: 'complete', querySelectorAll: () => [] } };
vm.runInNewContext(fs.readFileSync(path.join(__dirname, '../assets/js/addons.js'), 'utf8'), ctx);
const { lineTotal, formatPrice } = ctx.window.addonsSubtotalMath;

const eur = { decimals: 2, decimalSeparator: ',', thousandSeparator: '.', symbol: 'EUR', priceFormat: '%2$s %1$s' };
const usd = { decimals: 2, decimalSeparator: '.', thousandSeparator: ',', symbol: '$', priceFormat: '%1$s%2$s' };
const jpy = { decimals: 0, decimalSeparator: '.', thousandSeparator: ',', symbol: 'Y', priceFormat: '%1$s%2$s' };

// Same case as tests/cart-item-price-check.php: 20 + 5 wrap.
assert.strictEqual(lineTotal(20, [5], 1, 1), 25);
assert.strictEqual(lineTotal(20, [5, 0, 2.5], 3, 1), 82.5);
assert.strictEqual(lineTotal(20, [], 2, 1), 40);

assert.strictEqual(formatPrice(82.5, usd), '$82.50');
assert.strictEqual(formatPrice(1234567.891, eur), '1.234.567,89 EUR');
assert.strictEqual(formatPrice(1.005, usd), '$1.01');
assert.strictEqual(formatPrice(0.1 + 0.2, usd), '$0.30');
assert.strictEqual(formatPrice(1499.5, jpy), 'Y1,500');

assert.strictEqual(formatPrice(0.615, usd), '$0.62');

const fixture = JSON.parse(fs.readFileSync(path.join(__dirname, 'tax-modes.json'), 'utf8'));
const modes = new Set();
let failures = 0;
fixture.cases.forEach((c) => {
	modes.add(c.mode);
	const shown = formatPrice(lineTotal(c.base, c.extras, c.qty, c.factor), fixture.format);
	if (shown !== c.cart) {
		failures++;
		console.log(`FAIL ${c.mode}: ${c.base} + [${c.extras}] x ${c.qty} shows ${shown}, cart ${c.cart}`);
	}
});
assert.strictEqual(modes.size, 4, 'all four tax setups covered');
fixture.metas.forEach((m) => {
	[['cart', m.shown], ['order', m.order]].forEach(([where, got]) => {
		if (got !== `x (${m.cart})`) {
			failures++;
			console.log(`FAIL ${m.mode}: ${where} shows choice price ${m.price} as ${JSON.stringify(got)}, the cart line for it is ${m.cart}`);
		}
	});
});
assert.ok(fixture.metas.length > 0, 'cart and order meta covered');
assert.strictEqual(failures, 0, `${failures} prices differ from the cart`);

console.log(`OK: subtotal math and formatting match the cart (${fixture.cases.length} cart lines, ${fixture.metas.length} cart and order choice prices, 4 tax setups)`);
