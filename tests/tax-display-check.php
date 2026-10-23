<?php

/**
 * The live subtotal must equal the cart line in every tax setup: prices entered
 * with or without tax, displayed with or without tax, 23% rate. The price
 * beside a choice in the cart and in the order meta must be the price the cart
 * shows for that amount on its own.
 *
 * Runs inside a WooCommerce site and prints the cart's own figures as JSON for
 * tests/subtotal-check.js, which asserts the storefront math against them:
 *
 *   npx wp-env run cli wp eval-file wp-content/plugins/aldono/tests/tax-display-check.php > tests/tax-modes.json
 *   node tests/subtotal-check.js
 *
 * It changes tax options and adds a 23% standard rate and a draft product;
 * it restores the options and deletes both when done. Use a throwaway site.
 *
 * @package Addons/Tests
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Test script.

use WPPoland\StorefrontKit\AddOns\ProductAddOnsEngine;

defined('ABSPATH') || exit;

wc_load_cart();

$options = array('woocommerce_calc_taxes', 'woocommerce_prices_include_tax', 'woocommerce_tax_display_shop', 'woocommerce_tax_display_cart');
$saved   = array();
foreach ($options as $option) {
    $saved[$option] = get_option($option);
}

$rate_id = WC_Tax::_insert_tax_rate(
    array(
        'tax_rate_country'  => '',
        'tax_rate'          => '23.0000',
        'tax_rate_name'     => 'VAT',
        'tax_rate_priority' => 1,
        'tax_rate_shipping' => 1,
        'tax_rate_order'    => 0,
        'tax_rate_class'    => '',
    )
);

$product = new WC_Product_Simple();
$product->set_name('Tax check');
$product->set_status('draft');
$product->set_tax_status('taxable');
$product->set_regular_price('1');
$product->save();

$bases  = array(10, 19.99, 0.5, 33.33, 99.95, 1234.56);
$extras = array(array(), array(10), array(2.5, 0.05), array(4.99, 7.77), array(0.01));
$qtys   = array(1, 2, 3, 7);
$cases  = array();
$metas  = array();

update_option('woocommerce_calc_taxes', 'yes');

foreach (array('no', 'yes') as $entered_incl) {
    foreach (array('incl', 'excl') as $display) {
        update_option('woocommerce_prices_include_tax', $entered_incl);
        update_option('woocommerce_tax_display_shop', $display);
        update_option('woocommerce_tax_display_cart', $display);

        foreach ($bases as $base) {
            $product->set_price((string) $base);
            $factor = ProductAddOnsEngine::displayFactor($product);

            foreach ($extras as $extra) {
                // As applyCartItemPrice() prices the line.
                $line = clone $product;
                $line->set_price((string) ($base + array_sum($extra)));

                foreach ($extra as $price) {
                    $alone = clone $product;
                    $alone->set_price((string) $price);
                    $values = array(
                        'data'              => $line,
                        'addons_selections' => array(array('label' => 'Opt', 'value' => 'x', 'price' => $price)),
                    );
                    $shown  = apply_filters('woocommerce_get_item_data', array(), $values);
                    $item   = new WC_Order_Item_Product();
                    do_action('woocommerce_checkout_create_order_line_item', $item, 'key', $values, new WC_Order());
                    $metas[] = array(
                        'mode'  => ('yes' === $entered_incl ? 'entered incl' : 'entered excl') . ', shown ' . $display,
                        'price' => $price,
                        'cart'  => trim(html_entity_decode(wp_strip_all_tags((string) preg_replace('#<small.*</small>#s', '', WC()->cart->get_product_subtotal($alone, 1))), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                        'shown' => (string) ($shown[0]['value'] ?? ''),
                        'order' => (string) $item->get_meta('Opt'),
                    );
                }

                foreach ($qtys as $qty) {
                    $html    = WC()->cart->get_product_subtotal($line, $qty);
                    $html    = (string) preg_replace('#<small.*</small>#s', '', $html);
                    $cases[] = array(
                        'mode'   => ('yes' === $entered_incl ? 'entered incl' : 'entered excl') . ', shown ' . $display,
                        'base'   => $base,
                        'extras' => $extra,
                        'qty'    => $qty,
                        'factor' => $factor,
                        'cart'   => trim(html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                    );
                }
            }
        }
    }
}

$product->delete(true);
WC_Tax::_delete_tax_rate($rate_id);
foreach ($saved as $option => $value) {
    update_option($option, $value);
}

echo wp_json_encode(
    array(
        'format' => array(
            'decimals'          => wc_get_price_decimals(),
            'decimalSeparator'  => wc_get_price_decimal_separator(),
            'thousandSeparator' => wc_get_price_thousand_separator(),
            'symbol'            => html_entity_decode(get_woocommerce_currency_symbol(), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'priceFormat'       => html_entity_decode(get_woocommerce_price_format(), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        ),
        'cases'  => $cases,
        'metas'  => $metas,
    ),
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
) . "\n";
