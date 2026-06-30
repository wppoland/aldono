<?php

declare(strict_types=1);

namespace Addons\Service;

use Addons\Contract\HasHooks;
use WPPoland\StorefrontKit\AddOns\ProductAddOnsEngine;

defined('ABSPATH') || exit;

/**
 * Thin adapter over the storefront-kit {@see ProductAddOnsEngine}.
 *
 * Supplies this plugin's text-domain ('plogins-addons'), option storage, the per-product
 * meta read closure and the front-end fields template. All add-on orchestration
 * (render under the form, validation, cart capture, price adjustment, cart/order
 * display) lives in the namespace-neutral engine; this class only wires
 * localisation, settings, the product-meta reader and the template path.
 */
final class AddOnsService implements HasHooks
{
    public const OPTION   = 'addons_settings';
    public const META_KEY = '_addons_definitions';

    private ?ProductAddOnsEngine $engine = null;

    public function __construct()
    {
        // The engine ships with storefront-kit >= 1.5.0. When present, wire it
        // with this plugin's text-domain / option / meta. Otherwise leave the
        // service inert (see registerHooks()).
        if (! class_exists(ProductAddOnsEngine::class)) {
            return;
        }

        $this->engine = new ProductAddOnsEngine(
            cartKey: 'addons_selections',
            fieldPrefix: 'addons_field_',
            fieldsTemplate: 'add-on-fields',
            labels: [
                'group_title'     => $this->groupTitle(),
                'required_error'  => __('Please complete the "{label}" option before adding to cart.', 'plogins-addons'),
                'min_chars_error' => __('The option "{label}" must be at least {min} characters long.', 'plogins-addons'),
                'max_chars_error' => __('The option "{label}" cannot exceed {max} characters.', 'plogins-addons'),
            ],
            isEnabled: fn (): bool => $this->isEnabled(),
            settings: fn (): array => $this->settings(),
            productMeta: fn (\WC_Product $product) => $this->productMeta($product),
            renderTemplate: function (string $template, array $context): void {
                $this->renderTemplate($template, $context);
            },
        );
    }

    public function registerHooks(): void
    {
        if ($this->engine instanceof ProductAddOnsEngine) {
            add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
            $this->engine->registerHooks();
            return;
        }

        // storefront-kit < 1.5.0 has no ProductAddOnsEngine. Bump the
        // `wppoland/storefront-kit` constraint (composer update) to enable
        // add-ons. No hooks are registered until the engine is present.
    }

    /**
     * Enqueue the lightweight front-end stylesheet wherever the add-on fields
     * can render. The engine prints them on `woocommerce_before_add_to_cart_button`
     * whenever a product form is shown, which includes the `[product_page]`
     * shortcode on an arbitrary page — not just the single-product template. We
     * therefore load on `is_product()` *or* when the current singular content
     * embeds that shortcode, so the fields are never unstyled (avoids CLS) while
     * staying off every other page (CWV / Plugin Check hygiene).
     */
    public function enqueueAssets(): void
    {
        if (! $this->isEnabled() || ! $this->shouldEnqueueFront()) {
            return;
        }

        wp_enqueue_style(
            'addons',
            \Addons\Plugin::instance()->url('assets/css/addons.css'),
            [],
            \Addons\VERSION,
        );

        // Presentation-only enhancement: toggles the "inscribed" state that
        // draws the ink underline when a field is filled. Defers, no deps.
        wp_enqueue_script(
            'addons',
            \Addons\Plugin::instance()->url('assets/js/addons.js'),
            [],
            \Addons\VERSION,
            [
                'strategy'  => 'defer',
                'in_footer' => true,
            ],
        );
    }

    private function isEnabled(): bool
    {
        return (bool) ($this->settings()['enabled'] ?? false);
    }

    /**
     * Whether the add-on stylesheet is needed on the current front-end request.
     */
    private function shouldEnqueueFront(): bool
    {
        if (function_exists('is_product') && is_product()) {
            return true;
        }

        // `[product_page]` renders a full product form on an arbitrary page,
        // where is_product() is false. Detect it on singular content only.
        if (! is_singular()) {
            return false;
        }

        $post = get_post();

        if (! $post instanceof \WP_Post || $post->post_content === '') {
            return false;
        }

        return has_shortcode($post->post_content, 'product_page');
    }

    /**
     * Heading rendered above the add-on fields. An empty value intentionally
     * hides the heading; the packaged default supplies the initial text.
     */
    private function groupTitle(): string
    {
        return trim((string) ($this->settings()['group_title'] ?? ''));
    }

    /**
     * Read the stored add-on definitions for a product (host owns the meta key).
     *
     * @return array<int, array<string, mixed>>
     */
    private function productMeta(\WC_Product $product): array
    {
        $raw         = $product->get_meta(self::META_KEY, true);
        $definitions = is_array($raw) ? $raw : [];

        /**
         * Filter active add-on definitions before the storefront engine renders
         * and validates them. PRO extensions can hide or reshape definitions
         * while keeping the free storage format stable.
         *
         * @param array<int, array<string, mixed>> $definitions Product add-ons.
         * @param \WC_Product                     $product     Current product.
         */
        $filtered = apply_filters('addons_product_definitions', $definitions, $product);

        return is_array($filtered) ? $filtered : $definitions;
    }

    /**
     * Stored settings merged over packaged defaults.
     *
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        $stored = get_option(self::OPTION, []);

        if (! is_array($stored)) {
            $stored = [];
        }

        /** @var array<string, mixed> $defaults */
        $defaults = require ADDONS_DIR . 'config/defaults.php';

        return array_merge($defaults, $stored);
    }

    /**
     * Render a packaged template, exposing the context as local variables.
     *
     * @param array<string, mixed> $context
     */
    private function renderTemplate(string $template, array $context): void
    {
        $file = ADDONS_DIR . 'templates/' . $template . '.php';

        if (! is_readable($file)) {
            return;
        }

        // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Controlled, internal template context only.
        extract($context, EXTR_SKIP);
        require $file;
    }
}
