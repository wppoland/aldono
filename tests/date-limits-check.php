<?php
/**
 * Date add-on limits (1.5.0): earliest and latest date in days from today,
 * enforced on add to cart and printed as the picker's min/max.
 *
 * Run inside wp-env:  wp eval-file wp-content/plugins/aldono/tests/date-limits-check.php
 */

use Addons\Service\AddOnsService;
use Addons\Vendor\StorefrontKit\AddOns\ProductAddOnsEngine;

$fail  = 0;
$check = static function (string $label, bool $ok) use (&$fail): void {
    echo ($ok ? 'ok   ' : 'FAIL ') . $label . "\n";
    $fail += $ok ? 0 : 1;
};

$product = new WC_Product_Simple();
$product->set_name('date limits probe');
$product->set_regular_price('10');
$product->update_meta_data(AddOnsService::META_KEY, [
    ['label' => 'Delivery day', 'type' => 'date', 'required' => true, 'price' => 0, 'min_days' => 2, 'max_days' => 30],
    ['label' => 'Open date', 'type' => 'date', 'required' => false, 'price' => 0, 'min_days' => '', 'max_days' => ''],
]);
$product->save();
$id = $product->get_id();

$day = static fn (int $d): string => ProductAddOnsEngine::dateFromToday($d);
$ref = new ReflectionClass(ProductAddOnsEngine::class);
$_REQUEST[$ref->getConstant('NONCE_FIELD')] = wp_create_nonce($ref->getConstant('NONCE_ACTION'));

$validate = static function (string $date, string $open = '') use ($id): bool {
    $_REQUEST['addons_field_0'] = $date;
    $_REQUEST['addons_field_1'] = $open;
    wc_clear_notices();

    return (bool) apply_filters('woocommerce_add_to_cart_validation', true, $id, 1);
};

$check('inside the limits is accepted', $validate($day(5)));
$check('the earliest day itself is accepted', $validate($day(2)));
$check('the latest day itself is accepted', $validate($day(30)));
$check('before the earliest day is refused', ! $validate($day(1)));
$check('after the latest day is refused', ! $validate($day(31)));
$check('the refusal names the field', str_contains(wp_strip_all_tags(implode(' ', array_column(wc_get_notices('error'), 'notice'))), 'Delivery day'));
$check('a field without limits takes any date', $validate($day(5), '2001-01-01'));

$GLOBALS['product'] = wc_get_product($id);
$GLOBALS['post']    = get_post($id);
ob_start();
do_action('woocommerce_before_add_to_cart_button');
$html = ob_get_clean();
$check('the picker carries min and max', str_contains($html, 'min="' . $day(2) . '"') && str_contains($html, 'max="' . $day(30) . '"'));
$check('a field without limits has no min', substr_count($html, 'min="') === 1);

unset($_REQUEST['addons_field_0'], $_REQUEST['addons_field_1']);
wc_clear_notices();
$product->delete(true);
echo $fail ? "date-limits-check: $fail FAIL\n" : "date-limits-check: OK\n";
