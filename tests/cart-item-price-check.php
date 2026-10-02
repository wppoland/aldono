<?php

/**
 * The add-on price must be on the cart line's product object as soon as the
 * line exists, so every renderer sees it, not only a totals recalculation.
 *
 * The price used to be set in woocommerce_before_calculate_totals. The mini
 * cart and the cart fragments render without recalculating, so they listed
 * "1 x 20.00" under a subtotal that included the add-ons. With the master
 * switch off the cart still listed "Wrap (5.00)" although nothing was charged.
 *
 * Run: php tests/cart-item-price-check.php
 */

declare(strict_types=1);

namespace {
    define('ABSPATH', __DIR__);

    $filters = [];

    function add_filter(string $hook, mixed $cb, int $priority = 10, int $args = 1): void
    {
        $GLOBALS['filters'][$hook] = $cb;
    }

    function add_action(string $hook, mixed $cb, int $priority = 10, int $args = 1): void
    {
        $GLOBALS['filters'][$hook] = $cb;
    }

    function wc_price(float $price): string
    {
        return '<span>&euro;' . number_format($price, 2) . '</span>';
    }

    function wp_strip_all_tags(string $text): string
    {
        return strip_tags($text);
    }

    class WC_Product
    {
        public function __construct(private string $price)
        {
        }

        public function get_price(string $context = 'view'): string
        {
            return $this->price;
        }

        public function set_price(string $price): void
        {
            $this->price = $price;
        }
    }

    require __DIR__ . '/../lib/storefront-kit/AddOns/ProductAddOnsEngine.php';

    $enabled = true;
    $engine  = new \WPPoland\StorefrontKit\AddOns\ProductAddOnsEngine(
        'addons_selections',
        'addons_field_',
        '',
        [],
        static function () use (&$enabled): bool {
            return $enabled;
        },
        static fn (): array => [],
        static fn ($product): mixed => null,
        static function (string $template, array $args): void {
        },
    );
    $engine->registerHooks();

    $line = static fn (): array => [
        'data'              => new WC_Product('20'),
        'addons_selections' => [['label' => 'Wrap', 'value' => 'Wrap', 'price' => 5.0]],
    ];

    $failures = 0;
    foreach (['woocommerce_add_cart_item', 'woocommerce_get_cart_item_from_session'] as $hook) {
        if (! isset($filters[$hook])) {
            echo "FAIL: nothing prices the line on {$hook}\n";
            $failures++;
            continue;
        }
        $item = call_user_func($filters[$hook], $line());
        if ('25' !== $item['data']->get_price()) {
            echo "FAIL: {$hook} left the line at {$item['data']->get_price()}, expected 25\n";
            $failures++;
        }
    }

    $shown = $engine->displayCartItemData([], $line());
    if ('Wrap (€5.00)' !== ($shown[0]['value'] ?? '')) {
        echo 'FAIL: cart shows ' . var_export($shown[0]['value'] ?? null, true) . ", expected 'Wrap (€5.00)' with no entity\n";
        $failures++;
    }

    $enabled = false;
    if ([] !== $engine->displayCartItemData([], $line())) {
        echo "FAIL: switched off, the cart still lists a priced choice nothing charges\n";
        $failures++;
    }

    echo 0 === $failures ? "OK: add-on price is on the line from the start, shown as plain text\n" : '';
    exit($failures > 0 ? 1 : 0);
}
