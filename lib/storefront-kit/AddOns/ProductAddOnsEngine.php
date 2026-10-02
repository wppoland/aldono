<?php

declare(strict_types=1);

namespace WPPoland\StorefrontKit\AddOns;

/**
 * Namespace-neutral per-product add-ons engine (powers the Add-Ons, Product
 * Options for WooCommerce plugin).
 *
 * An admin defines a list of add-on fields per product (label, type
 * text, textarea, number, date, checkbox, select or radio, optional
 * per-option price delta); the engine renders
 * them under the product form, validates and captures the customer's choices
 * into the cart line item, adjusts the line price by the summed deltas, and
 * exposes the selections for cart / order display. Add-ons are stored as product
 * meta, the host owns the meta key and read/write, injected via the
 * `productMeta` closure (no custom table). Everything WooCommerce/
 * text-domain/option/meta specific is constructor-injected, mirroring
 * {@see \WPPoland\StorefrontKit\Badge\BadgeEngine} and
 * {@see \WPPoland\StorefrontKit\Pricing\DynamicPricingEngine}.
 *
 * The add-on field markup ships in the consuming plugin via the injected
 * `renderTemplate` closure.
 */
final class ProductAddOnsEngine
{
    private const SUPPORTED_TYPES = ['text', 'textarea', 'number', 'date', 'checkbox', 'select', 'radio'];

    private const NONCE_ACTION = 'addons_add_to_cart';

    private const NONCE_FIELD = 'addons_nonce';

    /**
     * @param \Closure(): bool $isEnabled
     * @param \Closure(): array<string, mixed> $settings Resolved settings.
     * @param \Closure(\WC_Product): mixed $productMeta Reads the raw add-on
     *        definition for a product (returns the stored array or null).
     * @param \Closure(string, array<string, mixed>): void $renderTemplate
     *        Echoes the add-on fields template under the product form.
     * @param string $cartKey Cart-item data key the selections are stored under.
     * @param string $fieldPrefix Request-field name prefix for posted add-ons.
     * @param array<string, string> $labels Fallback strings keyed by
     *        `required_error`, `group_title`.
     */
    public function __construct(
        private readonly string $cartKey,
        private readonly string $fieldPrefix,
        private readonly string $fieldsTemplate,
        private readonly array $labels,
        private readonly \Closure $isEnabled,
        private readonly \Closure $settings,
        private readonly \Closure $productMeta,
        private readonly \Closure $renderTemplate,
    ) {
    }

    public function registerHooks(): void
    {
        add_action('woocommerce_before_add_to_cart_button', [$this, 'renderFields'], 10);
        add_filter('woocommerce_add_to_cart_validation', [$this, 'validate'], 10, 3);
        add_filter('woocommerce_add_cart_item_data', [$this, 'captureCartItemData'], 10, 2);
        add_filter('woocommerce_get_item_data', [$this, 'displayCartItemData'], 10, 2);
        // Price the line once per product object, when it enters the cart and
        // each time the cart is rebuilt from the session. A price set only in
        // woocommerce_before_calculate_totals never reached the mini cart or the
        // cart fragments, which render without recalculating.
        add_filter('woocommerce_add_cart_item', [$this, 'applyCartItemPrice'], 20);
        add_filter('woocommerce_get_cart_item_from_session', [$this, 'applyCartItemPrice'], 20);
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'addOrderLineItemMeta'], 10, 4);
    }

    public function renderFields(): void
    {
        global $product;

        if (! $product instanceof \WC_Product || ! $this->isEnabled()) {
            return;
        }

        $addOns = $this->getAddOns($product);

        if ($addOns === []) {
            return;
        }

        wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD, false);

        ($this->renderTemplate)($this->fieldsTemplate, [
            'product' => $product,
            'add_ons' => $addOns,
            'field_prefix' => $this->fieldPrefix,
            'settings' => $this->getSettings(),
            'group_title' => $this->message('group_title'),
        ]);
    }

    /**
     * @param bool $passed
     * @param int $productId
     * @param int $quantity
     */
    public function validate($passed, $productId, $quantity): bool
    {
        if (! $this->isEnabled() || ! $passed) {
            return (bool) $passed;
        }

        $product = wc_get_product($productId);

        if (! $product instanceof \WC_Product) {
            return true;
        }

        $addOns = $this->getAddOns($product);

        // A form rendered with the add-on fields carries a nonce. One that is
        // present but no longer valid (an old cached page) is refused; without a
        // nonce no add-on input is read at all, see postedValue().
        if ($addOns !== [] && $this->nonceState() === 'invalid') {
            wc_add_notice($this->message('expired_error'), 'error');

            return false;
        }

        foreach ($addOns as $index => $addOn) {
            $value = $this->postedValue($index, $addOn['type']);

            if ($value !== '' && ! $this->isAllowedValue($addOn, $value)) {
                wc_add_notice(
                    \WPPoland\StorefrontKit\Support\Formatter::interpolate(
                        $this->message(match ($addOn['type']) {
                            'number' => 'number_error',
                            'date' => 'date_error',
                            default => 'invalid_error',
                        }),
                        ['label' => $addOn['label']],
                    ),
                    'error',
                );

                return false;
            }

            if ($addOn['required'] && $value === '') {
                wc_add_notice(
                    \WPPoland\StorefrontKit\Support\Formatter::interpolate(
                        $this->message('required_error'),
                        ['label' => $addOn['label']],
                    ),
                    'error',
                );

                return false;
            }

            if ($value !== '' && in_array($addOn['type'], ['text', 'textarea'], true)) {
                $min = (int) ($addOn['min_chars'] ?? 0);
                $max = (int) ($addOn['max_chars'] ?? 0);
                $len = mb_strlen($value);

                if ($min > 0 && $len < $min) {
                    wc_add_notice(
                        \WPPoland\StorefrontKit\Support\Formatter::interpolate(
                            $this->message('min_chars_error'),
                            [
                                'label' => $addOn['label'],
                                'min'   => (string) $min,
                            ]
                        ),
                        'error'
                    );
                    return false;
                }

                if ($max > 0 && $len > $max) {
                    wc_add_notice(
                        \WPPoland\StorefrontKit\Support\Formatter::interpolate(
                            $this->message('max_chars_error'),
                            [
                                'label' => $addOn['label'],
                                'max'   => (string) $max,
                            ]
                        ),
                        'error'
                    );
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $cartItemData
     * @param int $productId
     * @return array<string, mixed>
     */
    public function captureCartItemData($cartItemData, $productId): array
    {
        if (! $this->isEnabled()) {
            return $cartItemData;
        }

        $product = wc_get_product($productId);

        if (! $product instanceof \WC_Product) {
            return $cartItemData;
        }

        $selections = [];

        foreach ($this->getAddOns($product) as $index => $addOn) {
            $value = $this->postedValue($index, $addOn['type']);

            if ($value === '' || ! $this->isAllowedValue($addOn, $value)) {
                continue;
            }

            $selections[] = [
                'label' => $addOn['label'],
                'value' => $value,
                'price' => $this->resolvePrice($addOn, $value),
            ];
        }

        if ($selections !== []) {
            $cartItemData[$this->cartKey] = $selections;
        }

        return $cartItemData;
    }

    /**
     * @param array<int, array{key: string, value: string}> $itemData
     * @param array<string, mixed> $cartItem
     * @return array<int, array{key: string, value: string}>
     */
    public function displayCartItemData($itemData, $cartItem): array
    {
        // Switched off: nothing is charged, so do not list priced choices.
        if (! $this->isEnabled()) {
            return $itemData;
        }

        $selections = $this->selectionsFromCartItem($cartItem);

        foreach ($selections as $selection) {
            $value = $selection['value'];

            if ($selection['price'] > 0) {
                $value .= ' (' . $this->plainPrice($selection['price']) . ')';
            }

            $itemData[] = [
                'key' => $selection['label'],
                'value' => $value,
            ];
        }

        return $itemData;
    }

    /**
     * @param array<string, mixed> $cartItem
     * @return array<string, mixed>
     */
    public function applyCartItemPrice($cartItem): array
    {
        if (! $this->isEnabled() || ! isset($cartItem[$this->cartKey]) || ! ($cartItem['data'] ?? null) instanceof \WC_Product) {
            return $cartItem;
        }

        $extra = 0.0;

        foreach ($this->selectionsFromCartItem($cartItem) as $selection) {
            $extra += $selection['price'];
        }

        if ($extra !== 0.0) {
            $base = (float) $cartItem['data']->get_price('edit');
            $cartItem['data']->set_price((string) ($base + $extra));
        }

        return $cartItem;
    }

    /**
     * @param \WC_Order_Item_Product $item
     * @param string $cartItemKey
     * @param array<string, mixed> $values
     * @param \WC_Order $order
     */
    public function addOrderLineItemMeta($item, $cartItemKey, $values, $order): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        foreach ($this->selectionsFromCartItem($values) as $selection) {
            $value = $selection['value'];

            if ($selection['price'] > 0) {
                $value .= ' (' . $this->plainPrice($selection['price']) . ')';
            }

            $item->add_meta_data($selection['label'], $value);
        }
    }

    /**
     * The price as plain text. wc_price() writes the currency symbol as an
     * entity (&euro;, &#122;&#322;), which the order meta kept and REST, CSV
     * exports and plain-text emails then showed raw.
     */
    private function plainPrice(float $price): string
    {
        return html_entity_decode(wp_strip_all_tags(wc_price($price)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Normalise the host-stored add-on definition into a typed list.
     *
     * @return list<array{label: string, type: string, required: bool, price: float, options: array<string, float>}>
     */
    public function getAddOns(\WC_Product $product): array
    {
        if (! $this->isEnabled()) {
            return [];
        }

        $raw = ($this->productMeta)($product);

        if (! is_array($raw)) {
            return [];
        }

        $addOns = [];

        foreach ($raw as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $label = trim((string) ($entry['label'] ?? ''));
            $type = (string) ($entry['type'] ?? 'text');

            if ($label === '' || ! in_array($type, self::SUPPORTED_TYPES, true)) {
                continue;
            }

            $options = [];

            if (isset($entry['options']) && is_array($entry['options'])) {
                foreach ($entry['options'] as $optionLabel => $optionPrice) {
                    $options[(string) $optionLabel] = (float) $optionPrice;
                }
            }

            $addOns[] = [
                'label' => $label,
                'type' => $type,
                'required' => (bool) ($entry['required'] ?? false),
                'price' => (float) ($entry['price'] ?? 0),
                'options' => $options,
                'min_chars' => isset($entry['min_chars']) ? max(0, (int) $entry['min_chars']) : 0,
                'max_chars' => isset($entry['max_chars']) ? max(0, (int) $entry['max_chars']) : 0,
            ];
        }

        return $addOns;
    }

    /**
     * @param array{label: string, type: string, required: bool, price: float, options: array<string, float>} $addOn
     */
    private function resolvePrice(array $addOn, string $value): float
    {
        if (in_array($addOn['type'], ['select', 'radio'], true) && isset($addOn['options'][$value])) {
            return $addOn['options'][$value];
        }

        return $addOn['price'];
    }

    /**
     * A select or radio must post one of its own choices, a checkbox its own
     * label, a number a number and a date a real Y-m-d date; anything else did
     * not come from the rendered form.
     *
     * @param array{label: string, type: string, options: array<string, float>} $addOn
     */
    private function isAllowedValue(array $addOn, string $value): bool
    {
        return match ($addOn['type']) {
            'select', 'radio' => isset($addOn['options'][$value]),
            'checkbox' => $value === $addOn['label'],
            'number' => is_numeric($value),
            'date' => $this->isDate($value),
            default => true,
        };
    }

    private function isDate(string $value): bool
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    /**
     * @return 'absent'|'valid'|'invalid'
     */
    private function nonceState(): string
    {
        if (! isset($_REQUEST[self::NONCE_FIELD])) {
            return 'absent';
        }

        $nonce = sanitize_text_field(wp_unslash((string) $_REQUEST[self::NONCE_FIELD]));

        return wp_verify_nonce($nonce, self::NONCE_ACTION) ? 'valid' : 'invalid';
    }

    private function postedValue(int $index, string $type): string
    {
        if ($this->nonceState() !== 'valid') {
            return '';
        }

        $key = $this->fieldPrefix . $index;

        // Nonce verified by nonceState() just above.
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        if (! isset($_REQUEST[$key])) {
            return '';
        }

        $raw = (string) wp_unslash($_REQUEST[$key]);
        // phpcs:enable WordPress.Security.NonceVerification.Recommended

        if ($type !== 'textarea') {
            return sanitize_text_field($raw);
        }

        // Keep the line breaks, as one "\n" each: the browser counts a line
        // break as one character against maxlength but posts it as "\r\n".
        return str_replace("\r\n", "\n", sanitize_textarea_field($raw));
    }

    /**
     * @param array<string, mixed> $cartItem
     * @return list<array{label: string, value: string, price: float}>
     */
    private function selectionsFromCartItem(array $cartItem): array
    {
        if (! isset($cartItem[$this->cartKey]) || ! is_array($cartItem[$this->cartKey])) {
            return [];
        }

        $selections = [];

        foreach ($cartItem[$this->cartKey] as $selection) {
            if (! is_array($selection)) {
                continue;
            }

            $selections[] = [
                'label' => (string) ($selection['label'] ?? ''),
                'value' => (string) ($selection['value'] ?? ''),
                'price' => (float) ($selection['price'] ?? 0),
            ];
        }

        return $selections;
    }

    private function isEnabled(): bool
    {
        return (bool) ($this->isEnabled)();
    }

    /**
     * @return array<string, mixed>
     */
    private function getSettings(): array
    {
        $settings = ($this->settings)();

        return is_array($settings) ? $settings : [];
    }

    private function message(string $labelKey): string
    {
        return $this->labels[$labelKey] ?? '';
    }
}
