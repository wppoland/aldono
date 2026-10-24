<?php
/**
 * Front-end add-on fields, rendered by the storefront-kit ProductAddOnsEngine on
 * `woocommerce_before_add_to_cart_button`.
 *
 * @var \WC_Product                                                                              $product
 * @var list<array{label: string, type: string, required: bool, price: float, options: array<string, float>, description?: string, placeholder?: string}> $add_ons
 * @var string                                                                                   $field_prefix
 * @var array<string, mixed>                                                                     $settings
 * @var string                                                                                   $group_title
 *
 * @package Addons/Templates
 */

declare(strict_types=1);

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template-scope variables supplied by the engine.

defined('ABSPATH') || exit;

if (! isset($add_ons) || ! is_array($add_ons) || $add_ons === [] || ! isset($product) || ! $product instanceof \WC_Product) {
    return;
}

$addons_group_title = isset($group_title) ? (string) $group_title : '';
$addons_prefix      = isset($field_prefix) ? (string) $field_prefix : 'addons_field_';

$addons_settings      = isset($settings) && is_array($settings) ? $settings : array();
$addons_show_prices   = (bool) ($addons_settings['show_prices'] ?? true);
$addons_show_required = (bool) ($addons_settings['show_required'] ?? true);
$addons_card_style    = (bool) ($addons_settings['card_style'] ?? true);

$addons_wrap_class = 'addons-fields' . ($addons_card_style ? ' addons-fields--card' : '');

// Add-on prices are stored as entered and added to the product price in the
// cart, so taxes apply to them like to the product. Labels show each price the
// way the store displays product prices.
$addons_display_price = static function (float $price) use ($product): float {
    return (float) wc_get_price_to_display($product, array('price' => $price));
};

// Live subtotal. The cart line is (product price + add-on prices) x quantity,
// converted for display once, so the script does the same with the raw prices
// and the factor from Engine::displayFactor(). Shown only when the shop and the
// cart display taxes the same way, so it never disagrees with the labels above
// it. Grouped and other product types post several quantities or none, so the
// subtotal is limited to simple and variable products. It stays off when the
// merchant hides add-on prices, as it would reveal them.
$addons_subtotal = $addons_show_prices
    && $product->is_type(array('simple', 'variable'))
    && (! wc_tax_enabled() || get_option('woocommerce_tax_display_shop') === get_option('woocommerce_tax_display_cart'));
?>
<div class="<?php echo esc_attr($addons_wrap_class); ?>">
    <?php if ($addons_group_title !== '') : ?>
        <p class="addons-fields__title"><?php echo esc_html($addons_group_title); ?></p>
    <?php endif; ?>

    <?php foreach ($add_ons as $addons_index => $addons_field) : ?>
        <?php
        $addons_name      = $addons_prefix . (int) $addons_index;
        $addons_id        = sanitize_html_class($addons_name);
        $addons_label     = (string) ($addons_field['label'] ?? '');
        $addons_type      = (string) ($addons_field['type'] ?? 'text');
        $addons_required  = (bool) ($addons_field['required'] ?? false);
        $addons_price     = (float) ($addons_field['price'] ?? 0);
        $addons_min_chars = isset($addons_field['min_chars']) ? (int) $addons_field['min_chars'] : 0;
        $addons_max_chars = isset($addons_field['max_chars']) ? (int) $addons_field['max_chars'] : 0;
        $addons_desc      = (string) ($addons_field['description'] ?? '');
        $addons_desc_id   = $addons_id . '-description';
        $addons_described = $addons_desc !== '' ? ' aria-describedby="' . esc_attr($addons_desc_id) . '"' : '';
        $addons_desc_html = $addons_desc !== '' ? '<span class="addons-field__description description" id="' . esc_attr($addons_desc_id) . '">' . esc_html($addons_desc) . '</span>' : '';
        $addons_hint      = (string) ($addons_field['placeholder'] ?? '');
        $addons_hint_attr = $addons_hint !== '' ? ' placeholder="' . esc_attr($addons_hint) . '"' : '';
        $addons_options   = isset($addons_field['options']) && is_array($addons_field['options'])
            ? $addons_field['options']
            : array();

        // Skip malformed definitions (no label) and selects or radios with no
        // choices: rendering them would produce empty, confusing controls.
        if ($addons_label === '') {
            continue;
        }

        if (($addons_type === 'select' || $addons_type === 'radio') && $addons_options === array()) {
            continue;
        }

        $addons_price_html = ($addons_show_prices && $addons_price > 0)
            ? '<span class="addons-field__price">(' . wc_price($addons_display_price($addons_price)) . ')</span>'
            : '';
        $addons_required_html = ($addons_required && $addons_show_required)
            ? '<abbr class="required" title="' . esc_attr__('required', 'aldono') . '">*</abbr>'
            : '';
        $addons_char_attrs = ($addons_min_chars > 0 ? ' minlength="' . esc_attr((string) $addons_min_chars) . '"' : '')
            . ($addons_max_chars > 0 ? ' maxlength="' . esc_attr((string) $addons_max_chars) . '"' : '');
        ?>
        <div class="addons-field addons-field--<?php echo esc_attr($addons_type); ?>">
            <?php if ($addons_type === 'checkbox') : ?>
                <label for="<?php echo esc_attr($addons_id); ?>">
                    <input
                        type="checkbox"
                        id="<?php echo esc_attr($addons_id); ?>"
                        name="<?php echo esc_attr($addons_name); ?>"
                        value="<?php echo esc_attr($addons_label); ?>"
                        data-addons-price="<?php echo esc_attr((string) $addons_price); ?>"
                        <?php echo $addons_required ? 'required' : ''; ?>
                        <?php echo $addons_described; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() above. ?>
                    />
                    <?php echo esc_html($addons_label); ?>
                    <?php echo wp_kses_post($addons_required_html); ?>
                    <?php echo wp_kses_post($addons_price_html); ?>
                </label>
                <?php echo $addons_desc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() and esc_html() above. ?>
            <?php elseif ($addons_type === 'radio') : ?>
                <fieldset class="addons-field__choices"<?php echo $addons_described; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() above. ?>>
                    <legend>
                        <?php echo esc_html($addons_label); ?>
                        <?php echo wp_kses_post($addons_required_html); ?>
                    </legend>
                    <?php echo $addons_desc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() and esc_html() above. ?>
                    <?php $addons_opt_i = 0; ?>
                    <?php foreach ($addons_options as $addons_opt_label => $addons_opt_price) : ?>
                        <?php
                        $addons_opt_label = (string) $addons_opt_label;
                        $addons_opt_price = (float) $addons_opt_price;
                        $addons_opt_id    = $addons_id . '-' . $addons_opt_i++;
                        ?>
                        <label for="<?php echo esc_attr($addons_opt_id); ?>">
                            <input
                                type="radio"
                                id="<?php echo esc_attr($addons_opt_id); ?>"
                                name="<?php echo esc_attr($addons_name); ?>"
                                value="<?php echo esc_attr($addons_opt_label); ?>"
                                data-addons-price="<?php echo esc_attr((string) $addons_opt_price); ?>"
                                <?php echo $addons_required ? 'required' : ''; ?>
                            />
                            <?php echo esc_html($addons_opt_label); ?>
                            <?php if ($addons_show_prices && $addons_opt_price > 0) : ?>
                                <span class="addons-field__price">(<?php echo wp_kses_post(wc_price($addons_display_price($addons_opt_price))); ?>)</span>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
            <?php elseif ($addons_type === 'select') : ?>
                <label for="<?php echo esc_attr($addons_id); ?>">
                    <?php echo esc_html($addons_label); ?>
                    <?php echo wp_kses_post($addons_required_html); ?>
                </label>
                <?php echo $addons_desc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() and esc_html() above. ?>
                <select
                    id="<?php echo esc_attr($addons_id); ?>"
                    name="<?php echo esc_attr($addons_name); ?>"
                    <?php echo $addons_required ? 'required' : ''; ?>
                    <?php echo $addons_described; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() above. ?>
                >
                    <option value=""><?php esc_html_e('- Select -', 'aldono'); ?></option>
                    <?php foreach ($addons_options as $addons_opt_label => $addons_opt_price) : ?>
                        <?php
                        $addons_opt_label = (string) $addons_opt_label;
                        $addons_opt_price = (float) $addons_opt_price;
                        $addons_opt_text  = ($addons_show_prices && $addons_opt_price > 0)
                            ? $addons_opt_label . ' (' . wp_strip_all_tags(wc_price($addons_display_price($addons_opt_price))) . ')'
                            : $addons_opt_label;
                        ?>
                        <option value="<?php echo esc_attr($addons_opt_label); ?>" data-addons-price="<?php echo esc_attr((string) $addons_opt_price); ?>"><?php echo esc_html($addons_opt_text); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else : ?>
                <label for="<?php echo esc_attr($addons_id); ?>">
                    <?php echo esc_html($addons_label); ?>
                    <?php echo wp_kses_post($addons_required_html); ?>
                    <?php echo wp_kses_post($addons_price_html); ?>
                </label>
                <?php echo $addons_desc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() and esc_html() above. ?>
                <?php if ($addons_type === 'textarea') : ?>
                    <textarea
                        id="<?php echo esc_attr($addons_id); ?>"
                        name="<?php echo esc_attr($addons_name); ?>"
                        class="input-text"
                        data-addons-price="<?php echo esc_attr((string) $addons_price); ?>"
                        rows="3"
                        <?php echo $addons_required ? 'required' : ''; ?>
                        <?php echo $addons_char_attrs . $addons_hint_attr . $addons_described; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() values above. ?>
                    ></textarea>
                <?php elseif ($addons_type === 'number') : ?>
                    <input
                        type="number"
                        step="any"
                        id="<?php echo esc_attr($addons_id); ?>"
                        name="<?php echo esc_attr($addons_name); ?>"
                        class="input-text"
                        data-addons-price="<?php echo esc_attr((string) $addons_price); ?>"
                        <?php echo $addons_required ? 'required' : ''; ?>
                        <?php echo $addons_hint_attr . $addons_described; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() values above. ?>
                    />
                <?php elseif ($addons_type === 'date') : ?>
                    <input
                        type="date"
                        id="<?php echo esc_attr($addons_id); ?>"
                        name="<?php echo esc_attr($addons_name); ?>"
                        class="input-text"
                        data-addons-price="<?php echo esc_attr((string) $addons_price); ?>"
                        <?php echo $addons_required ? 'required' : ''; ?>
                        <?php echo $addons_described; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() above. ?>
                    />
                <?php else : ?>
                    <input
                        type="text"
                        id="<?php echo esc_attr($addons_id); ?>"
                        name="<?php echo esc_attr($addons_name); ?>"
                        class="input-text"
                        data-addons-price="<?php echo esc_attr((string) $addons_price); ?>"
                        <?php echo $addons_required ? 'required' : ''; ?>
                        <?php echo $addons_char_attrs . $addons_hint_attr . $addons_described; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() values above. ?>
                    />
                <?php endif; ?>
                <?php if (($addons_type === 'text' || $addons_type === 'textarea') && ($addons_min_chars > 0 || $addons_max_chars > 0)) : ?>
                    <small class="addons-char-counter-wrap description">
                        <span class="addons-char-counter" data-addons-char-counter data-min="<?php echo esc_attr((string) $addons_min_chars); ?>" data-max="<?php echo esc_attr((string) $addons_max_chars); ?>" aria-live="polite"></span>
                    </small>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if ($addons_subtotal) : ?>
        <p
            class="addons-subtotal"
            aria-live="polite"
            aria-atomic="true"
            data-addons-subtotal
            data-base="<?php echo esc_attr($product->is_type('variable') ? '' : (string) $product->get_price()); ?>"
            data-tax-factor="<?php echo esc_attr((string) \WPPoland\StorefrontKit\AddOns\ProductAddOnsEngine::displayFactor($product)); ?>"
            data-decimals="<?php echo esc_attr((string) wc_get_price_decimals()); ?>"
            data-decimal-separator="<?php echo esc_attr(wc_get_price_decimal_separator()); ?>"
            data-thousand-separator="<?php echo esc_attr(wc_get_price_thousand_separator()); ?>"
            data-symbol="<?php echo esc_attr(html_entity_decode(get_woocommerce_currency_symbol(), ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>"
            data-price-format="<?php echo esc_attr(html_entity_decode(get_woocommerce_price_format(), ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>"
            data-label="<?php /* translators: %s: the formatted subtotal, for example 25.00 EUR. */ echo esc_attr__('Subtotal: %s', 'aldono'); ?>"
        ></p>
    <?php endif; ?>
</div>
<?php
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
