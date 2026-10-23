/**
 * The live subtotal must show the number the cart will charge:
 * (unit price + add-on prices) x quantity, formatted like wc_price().
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
assert.strictEqual(lineTotal(20, [5], 1), 25);
assert.strictEqual(lineTotal(20, [5, 0, 2.5], 3), 82.5);
assert.strictEqual(lineTotal(20, [], 2), 40);

assert.strictEqual(formatPrice(82.5, usd), '$82.50');
assert.strictEqual(formatPrice(1234567.891, eur), '1.234.567,89 EUR');
assert.strictEqual(formatPrice(1.005, usd), '$1.01');
assert.strictEqual(formatPrice(0.1 + 0.2, usd), '$0.30');
assert.strictEqual(formatPrice(1499.5, jpy), 'Y1,500');

console.log('OK: subtotal math and formatting match the cart');
