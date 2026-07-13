<?php
/**
 * Front-end add-on fields, rendered by the storefront-kit ProductAddOnsEngine on
 * `woocommerce_before_add_to_cart_button`.
 *
 * @var \WC_Product                                                                              $product
 * @var list<array{label: string, type: string, required: bool, price: float, options: array<string, float>}> $add_ons
 * @var string                                                                                   $field_prefix
 * @var array<string, mixed>                                                                     $settings
 * @var string                                                                                   $group_title
 *
 * @package Addons/Templates
 */

declare(strict_types=1);

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template-scope variables supplied by the engine.

defined('ABSPATH') || exit;

if (! isset($add_ons) || ! is_array($add_ons) || $add_ons === []) {
    return;
}

$addons_group_title = isset($group_title) ? (string) $group_title : '';
$addons_prefix      = isset($field_prefix) ? (string) $field_prefix : 'addons_field_';

$addons_settings      = isset($settings) && is_array($settings) ? $settings : array();
$addons_show_prices   = (bool) ($addons_settings['show_prices'] ?? true);
$addons_show_required = (bool) ($addons_settings['show_required'] ?? true);
$addons_card_style    = (bool) ($addons_settings['card_style'] ?? true);

$addons_wrap_class = 'addons-fields' . ($addons_card_style ? ' addons-fields--card' : '');
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
        $addons_options   = isset($addons_field['options']) && is_array($addons_field['options'])
            ? $addons_field['options']
            : array();

        // Skip malformed definitions (no label) and selects with no choices —
        // rendering them would produce empty, confusing controls.
        if ($addons_label === '') {
            continue;
        }

        if ($addons_type === 'select' && $addons_options === array()) {
            continue;
        }
        ?>
        <p class="addons-field addons-field--<?php echo esc_attr($addons_type); ?>">
            <?php if ($addons_type === 'checkbox') : ?>
                <label for="<?php echo esc_attr($addons_id); ?>">
                    <input
                        type="checkbox"
                        id="<?php echo esc_attr($addons_id); ?>"
                        name="<?php echo esc_attr($addons_name); ?>"
                        value="<?php echo esc_attr($addons_label); ?>"
                        <?php echo $addons_required ? 'required' : ''; ?>
                    />
                    <?php echo esc_html($addons_label); ?>
                    <?php if ($addons_show_prices && $addons_price > 0) : ?>
                        <span class="addons-field__price">(<?php echo wp_kses_post(wc_price($addons_price)); ?>)</span>
                    <?php endif; ?>
                </label>
            <?php elseif ($addons_type === 'select') : ?>
                <label for="<?php echo esc_attr($addons_id); ?>">
                    <?php echo esc_html($addons_label); ?>
                    <?php if ($addons_required && $addons_show_required) : ?><abbr class="required" title="<?php esc_attr_e('required', 'plogins-addons'); ?>">*</abbr><?php endif; ?>
                </label>
                <select
                    id="<?php echo esc_attr($addons_id); ?>"
                    name="<?php echo esc_attr($addons_name); ?>"
                    <?php echo $addons_required ? 'required' : ''; ?>
                >
                    <option value=""><?php esc_html_e(', Select, ', 'plogins-addons'); ?></option>
                    <?php foreach ($addons_options as $addons_opt_label => $addons_opt_price) : ?>
                        <?php
                        $addons_opt_label = (string) $addons_opt_label;
                        $addons_opt_price = (float) $addons_opt_price;
                        $addons_opt_text  = ($addons_show_prices && $addons_opt_price > 0)
                            ? $addons_opt_label . ' (' . wp_strip_all_tags(wc_price($addons_opt_price)) . ')'
                            : $addons_opt_label;
                        ?>
                        <option value="<?php echo esc_attr($addons_opt_label); ?>"><?php echo esc_html($addons_opt_text); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else : ?>
                <label for="<?php echo esc_attr($addons_id); ?>">
                    <?php echo esc_html($addons_label); ?>
                    <?php if ($addons_required && $addons_show_required) : ?><abbr class="required" title="<?php esc_attr_e('required', 'plogins-addons'); ?>">*</abbr><?php endif; ?>
                    <?php if ($addons_show_prices && $addons_price > 0) : ?>
                        <span class="addons-field__price">(<?php echo wp_kses_post(wc_price($addons_price)); ?>)</span>
                    <?php endif; ?>
                </label>
                <input
                    type="text"
                    id="<?php echo esc_attr($addons_id); ?>"
                    name="<?php echo esc_attr($addons_name); ?>"
                    class="input-text"
                    <?php echo $addons_required ? 'required' : ''; ?>
                    <?php echo $addons_min_chars > 0 ? 'minlength="' . esc_attr((string) $addons_min_chars) . '"' : ''; ?>
                    <?php echo $addons_max_chars > 0 ? 'maxlength="' . esc_attr((string) $addons_max_chars) . '"' : ''; ?>
                />
                <?php if ($addons_min_chars > 0 || $addons_max_chars > 0) : ?>
                    <small class="addons-char-counter-wrap description">
                        <span class="addons-char-counter" data-addons-char-counter data-min="<?php echo esc_attr((string) $addons_min_chars); ?>" data-max="<?php echo esc_attr((string) $addons_max_chars); ?>" aria-live="polite"></span>
                    </small>
                <?php endif; ?>
            <?php endif; ?>
        </p>
    <?php endforeach; ?>
</div>
<?php
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
