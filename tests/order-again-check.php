<?php
/**
 * "Order again" brings the add-ons of the old line back into the cart, matched
 * by label and priced at today's price; a required add-on with no stored value
 * keeps the line out. Express pay buttons hide on a product with add-ons.
 *
 * Run inside wp-env:  wp eval-file wp-content/plugins/aldono/tests/order-again-check.php
 */

use Addons\Service\AddOnsService;

$fail  = 0;
$check = static function (string $label, bool $ok) use (&$fail): void {
    echo ($ok ? 'ok   ' : 'FAIL ') . $label . "\n";
    $fail += $ok ? 0 : 1;
};

$customer = wp_insert_user(['user_login' => 'reorder_' . wp_generate_password(6, false), 'user_pass' => wp_generate_password(), 'role' => 'customer']);
wp_set_current_user($customer);
WC()->session = new WC_Session_Handler();
WC()->session->init();
WC()->cart = new WC_Cart();

$product = new WC_Product_Simple();
$product->set_name('order again probe');
$product->set_regular_price('10');
$product->update_meta_data(AddOnsService::META_KEY, [
    ['label' => 'Engraving', 'type' => 'text', 'required' => false, 'price' => 2, 'options' => []],
    ['label' => 'Wrap', 'type' => 'select', 'required' => false, 'price' => 0, 'options' => ['Gold' => 3.0, 'Silver' => 1.0]],
]);
$product->save();

// The order as checkout writes it.
$order = wc_create_order(['customer_id' => $customer]);
$item  = new WC_Order_Item_Product();
$item->set_product($product);
$item->set_quantity(1);
do_action('woocommerce_checkout_create_order_line_item', $item, 'k', [
    'data'              => $product,
    'addons_selections' => [
        ['label' => 'Engraving', 'value' => 'For Ana', 'price' => 2.0],
        ['label' => 'Wrap', 'value' => 'Gold', 'price' => 3.0],
    ],
], $order);
$order->add_item($item);
$order->set_status('completed');
$order->save();

$reorder = static function () use ($order): array {
    WC()->cart->empty_cart();
    wc_clear_notices();
    $session = new WC_Cart_Session(WC()->cart);
    $method  = new ReflectionMethod(WC_Cart_Session::class, 'populate_cart_from_order');
    $method->setAccessible(true);

    return array_values($method->invoke($session, $order->get_id(), []));
};

// Today the gold wrap costs more.
$product->update_meta_data(AddOnsService::META_KEY, [
    ['label' => 'Engraving', 'type' => 'text', 'required' => false, 'price' => 2, 'options' => []],
    ['label' => 'Wrap', 'type' => 'select', 'required' => false, 'price' => 0, 'options' => ['Gold' => 4.0, 'Silver' => 1.0]],
]);
$product->save();

$cart = $reorder();
// WooCommerce redirects after "Order again"; the next request rebuilds the line from the session.
$line = isset($cart[0]) ? apply_filters('woocommerce_get_cart_item_from_session', $cart[0], $cart[0], $cart[0]['key']) : [];
$values = array_column($line['addons_selections'] ?? [], 'value', 'label');
$check('the old line is back in the cart', 1 === count($cart));
$check('its add-ons come back', 'For Ana' === ($values['Engraving'] ?? '') && 'Gold' === ($values['Wrap'] ?? ''));
$check('priced today: 10 + 2 + 4 = 16, got ' . (isset($line['data']) ? $line['data']->get_price() : 'none'), isset($line['data']) && 16.0 === (float) $line['data']->get_price());

// A required add-on added since the order has no stored value: the line stays out.
$product->update_meta_data(AddOnsService::META_KEY, [
    ['label' => 'Engraving', 'type' => 'text', 'required' => false, 'price' => 2, 'options' => []],
    ['label' => 'Size', 'type' => 'select', 'required' => true, 'price' => 0, 'options' => ['S' => 0.0, 'L' => 0.0]],
]);
$product->save();
$check('a new required add-on keeps the line out', [] === $reorder());

// Express buttons.
$GLOBALS['wp_query']->is_singular = true;
$GLOBALS['wp_query']->is_single   = true;
$GLOBALS['wp_query']->queried_object = get_post($product->get_id());
$check('Stripe express hides on a product with add-ons', true === apply_filters('wc_stripe_hide_payment_request_on_product_page', false, get_post($product->get_id())));
$plain = new WC_Product_Simple();
$plain->set_name('plain probe');
$plain->set_regular_price('5');
$plain->save();
$check('Stripe express stays on a product without add-ons', false === apply_filters('wc_stripe_hide_payment_request_on_product_page', false, get_post($plain->get_id())));
$check('WooPayments express off for a product with add-ons on its page', false === apply_filters('wcpay_payment_request_is_product_supported', true, $product));
$check('WooPayments express on for a product without add-ons', true === apply_filters('wcpay_payment_request_is_product_supported', true, $plain));

WC()->cart->empty_cart();
$order->delete(true);
$product->delete(true);
$plain->delete(true);
require_once ABSPATH . 'wp-admin/includes/user.php';
wp_delete_user($customer);

echo 0 === $fail ? "order again: OK\n" : "order again: {$fail} failure(s)\n";
