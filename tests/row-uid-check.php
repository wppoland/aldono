<?php
/**
 * Every saved add-on row carries a stable id: a new row gets one, a posted id is
 * kept, and an id posted twice is replaced on the second row, so a rule that
 * names a row (Aldono Pro conditions) keeps naming it across saves.
 *
 * Run inside wp-env:  wp eval-file wp-content/plugins/aldono/tests/row-uid-check.php
 */

use Addons\Admin\ProductData;
use Addons\Service\AddOnsService;

$fail  = 0;
$check = static function (string $label, bool $ok) use (&$fail): void {
    echo ($ok ? 'ok   ' : 'FAIL ') . $label . "\n";
    $fail += $ok ? 0 : 1;
};

wp_set_current_user((int) (get_users(['role' => 'administrator', 'number' => 1, 'fields' => 'ID'])[0] ?? 1));
$product = new WC_Product_Simple();
$product->set_name('row uid probe');
$product->save();

$save = static function (array $rows) use ($product): array {
    $ref = new ReflectionClass(ProductData::class);
    $_POST = [
        $ref->getConstant('NONCE_NAME') => wp_create_nonce($ref->getConstant('NONCE_ACTION')),
        'addons_def' => $rows,
    ];
    $fresh = wc_get_product($product->get_id());
    (new ProductData())->save($fresh);
    $fresh->save();
    $_POST = [];

    return (array) wc_get_product($product->get_id())->get_meta(AddOnsService::META_KEY, true);
};

$saved = $save([
    ['uid' => 'p0', 'label' => 'Gift note', 'type' => 'text'],
    ['label' => 'Add engraving?', 'type' => 'checkbox'],
    ['uid' => 'p0', 'label' => 'Copy', 'type' => 'text'],
]);
$uids = array_column($saved, 'uid');
$check('a posted id is kept', 'p0' === ($uids[0] ?? ''));
$check('a new row gets an id', isset($uids[1]) && str_starts_with($uids[1], 'u') && strlen($uids[1]) > 5);
$check('an id posted twice is replaced', isset($uids[2]) && 'p0' !== $uids[2] && count(array_unique($uids)) === 3);

// Saved again as the editor posts it back: ids unchanged.
$again = array_column($save(array_map(static fn ($r) => ['uid' => $r['uid'], 'label' => $r['label'], 'type' => $r['type']], $saved)), 'uid');
$check('ids survive a second save', $again === $uids);

$check('positions follow render order by id', ['p0' => 0, $uids[1] => 1, $uids[2] => 2] === AddOnsService::fieldPositions($saved));

$product->delete(true);
echo 0 === $fail ? "row uid: OK\n" : "row uid: {$fail} failure(s)\n";
