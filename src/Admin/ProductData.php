<?php

declare(strict_types=1);

namespace Addons\Admin;

use Addons\Contract\HasHooks;
use Addons\Service\AddOnsService;

defined('ABSPATH') || exit;

/**
 * Per-product add-on editor in the WooCommerce "Product data" panel.
 *
 * Adds an "Add-Ons" product-data tab where the merchant defines a repeatable
 * list of add-ons (label, type text/checkbox/select, required flag, price delta,
 * and select options). Definitions are stored as the product meta key owned by
 * {@see AddOnsService::META_KEY} — no custom table.
 */
final class ProductData implements HasHooks
{
    private const NONCE_ACTION = 'addons_save_product_data';
    private const NONCE_NAME   = 'addons_product_data_nonce';

    public function registerHooks(): void
    {
        add_filter('woocommerce_product_data_tabs', [$this, 'addTab']);
        add_action('woocommerce_product_data_panels', [$this, 'renderPanel']);
        add_action('woocommerce_admin_process_product_object', [$this, 'save']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    /**
     * @param array<string, array<string, mixed>> $tabs
     * @return array<string, array<string, mixed>>
     */
    public function addTab(array $tabs): array
    {
        $tabs['addons'] = [
            'label'    => __('Add-Ons', 'addons'),
            'target'   => 'addons_product_data',
            'class'    => ['show_if_simple', 'show_if_variable'],
            'priority' => 80,
        ];

        return $tabs;
    }

    public function renderPanel(): void
    {
        global $post;

        $product = $post instanceof \WP_Post ? wc_get_product($post->ID) : null;
        $addOns  = $product instanceof \WC_Product ? $product->get_meta(AddOnsService::META_KEY, true) : [];

        if (! is_array($addOns)) {
            $addOns = [];
        }

        $file = ADDONS_DIR . 'templates/admin/product-data-panel.php';

        if (! is_readable($file)) {
            return;
        }

        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Controlled, internal template context only.
        extract(['add_ons' => $addOns], EXTR_SKIP);
        require $file;
    }

    /**
     * Persist the posted add-on definitions onto the product object.
     */
    public function save(\WC_Product $product): void
    {
        if (! isset($_POST[self::NONCE_NAME])) {
            return;
        }

        $nonce = sanitize_text_field(wp_unslash((string) $_POST[self::NONCE_NAME]));

        if (! wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            return;
        }

        // Defence in depth: WooCommerce already gates the product save on
        // edit_post, but verify the editor can manage this product before we
        // mutate its meta (guards against the hook firing in an unexpected flow).
        $productId = $product->get_id();

        if ($productId > 0 && ! current_user_can('edit_post', $productId)) {
            return;
        }

        $definitions = $this->collectDefinitions();

        if ($definitions === []) {
            $product->delete_meta_data(AddOnsService::META_KEY);
            return;
        }

        $product->update_meta_data(AddOnsService::META_KEY, $definitions);
    }

    /**
     * Sanitise the posted repeater rows into a clean definitions list.
     *
     * @return array<int, array<string, mixed>>
     */
    private function collectDefinitions(): array
    {
        // Nonce verified in save(); this only reads the already-validated request.
        // Each scalar field is sanitised individually below (label, type, price,
        // options), so the raw array is unslashed here without a blanket sanitiser.
        // phpcs:disable WordPress.Security.NonceVerification.Missing
        if (! isset($_POST['addons_def']) || ! is_array($_POST['addons_def'])) {
            return [];
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Per-field sanitisation happens below in the loop.
        $rows = wp_unslash($_POST['addons_def']);
        // phpcs:enable WordPress.Security.NonceVerification.Missing

        if (! is_array($rows)) {
            return [];
        }

        /**
         * Filter supported add-on field types before product definitions are
         * sanitised. Premium extensions can register extra types while the free
         * plugin keeps ownership of the storage format.
         *
         * @param array<int, string> $supportedTypes Supported field type keys.
         */
        $supportedTypes = apply_filters('addons_supported_definition_types', ['text', 'checkbox', 'select']);

        if (! is_array($supportedTypes)) {
            $supportedTypes = ['text', 'checkbox', 'select'];
        }
        $definitions    = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = isset($row['label']) ? sanitize_text_field((string) $row['label']) : '';
            $type  = isset($row['type']) ? sanitize_key((string) $row['type']) : 'text';

            if ($label === '' || ! in_array($type, $supportedTypes, true)) {
                continue;
            }

            $definition = [
                'label'    => $label,
                'type'     => $type,
                'required' => ! empty($row['required']),
                // Add-on deltas are *added* to the line price by the engine. A
                // negative delta would let a buyer drive the line (and cart)
                // total below the base price, so clamp to a non-negative surcharge.
                'price'    => isset($row['price']) ? max(0.0, (float) wc_format_decimal((string) $row['price'])) : 0.0,
                'options'  => [],
            ];

            if ($type === 'select' && isset($row['options']) && is_string($row['options'])) {
                $definition['options'] = $this->parseOptions($row['options']);
            }

            /**
             * Filter a single product add-on definition before it is persisted.
             *
             * PRO extensions use this hook to attach their own metadata (for
             * example conditional-logic rules) without forking the free editor.
             *
             * @param array<string, mixed> $definition Sanitised definition.
             * @param array<string, mixed> $row        Raw posted row.
             */
            $definition = apply_filters('addons_sanitize_definition', $definition, $row);

            if (is_array($definition)) {
                $definitions[] = $definition;
            }
        }

        return $definitions;
    }

    /**
     * Parse the textarea options ("Label | 2.50" per line) into label => price.
     *
     * @return array<string, float>
     */
    private function parseOptions(string $raw): array
    {
        $options = [];

        foreach (preg_split('/\r\n|\r|\n/', $raw) ?: [] as $line) {
            $line = trim((string) $line);

            if ($line === '') {
                continue;
            }

            $parts = explode('|', $line);
            $label = sanitize_text_field(trim($parts[0]));
            // Clamp to a non-negative surcharge — see collectDefinitions().
            $price = isset($parts[1]) ? max(0.0, (float) wc_format_decimal(trim($parts[1]))) : 0.0;

            if ($label !== '') {
                $options[$label] = $price;
            }
        }

        return $options;
    }

    public function enqueueAssets(string $hook): void
    {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        if ($screen === null || $screen->post_type !== 'product') {
            return;
        }

        wp_enqueue_style(
            'addons-admin',
            \Addons\Plugin::instance()->url('assets/css/admin.css'),
            [],
            \Addons\VERSION,
        );

        wp_enqueue_script(
            'addons-admin',
            \Addons\Plugin::instance()->url('assets/js/admin.js'),
            [],
            \Addons\VERSION,
            [
                'in_footer' => true,
                'strategy'  => 'defer',
            ],
        );
    }
}
