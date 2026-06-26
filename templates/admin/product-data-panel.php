<?php
/**
 * "Add-Ons" product-data panel in the WooCommerce product editor.
 *
 * @var array<int, array<string, mixed>> $add_ons Stored add-on definitions.
 *
 * @package Addons/Templates
 */

declare(strict_types=1);

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template-scope variables supplied by ProductData.

use Addons\Admin\InlineHelp;

defined('ABSPATH') || exit;

$addons_rows = isset($add_ons) && is_array($add_ons) ? $add_ons : array();
?>
<div id="addons_product_data" class="panel woocommerce_options_panel addons-admin">
    <div class="options_group">
        <p class="form-field">
            <label><?php esc_html_e('Product add-ons', 'addons'); ?>
                <?php InlineHelp::output('panel-intro', __('Each row below becomes one field a customer sees on this product page. Use Text for free-form input (like an engraving message), Checkbox for a yes/no extra, or Select for a list of choices. Any price you enter is added to the line total when the option is chosen.', 'addons')); ?>
            </label>
            <span class="description">
                <?php esc_html_e('Define options customers can choose before adding this product to the cart. A positive price is added to the line total.', 'addons'); ?>
            </span>
        </p>

        <div class="addons-empty" data-addons-empty<?php echo $addons_rows === array() ? '' : ' hidden'; ?>>
            <div class="addons-empty__icon" aria-hidden="true">&#10133;</div>
            <p><strong><?php esc_html_e('No add-ons yet', 'addons'); ?></strong></p>
            <p><?php esc_html_e('Add your first option to let customers personalise this product or pay for extras such as gift wrapping, engraving, or express handling.', 'addons'); ?></p>
        </div>

        <div class="addons-admin-rows" data-addons-rows>
            <?php foreach ($addons_rows as $addons_i => $addons_row) : ?>
                <?php
                $addons_label    = (string) ($addons_row['label'] ?? '');
                $addons_type     = (string) ($addons_row['type'] ?? 'text');
                $addons_required = ! empty($addons_row['required']);
                $addons_price    = (string) ($addons_row['price'] ?? '');
                $addons_options  = '';

                if (isset($addons_row['options']) && is_array($addons_row['options'])) {
                    $addons_lines = array();
                    foreach ($addons_row['options'] as $addons_opt_label => $addons_opt_price) {
                        $addons_lines[] = $addons_opt_label . ' | ' . $addons_opt_price;
                    }
                    $addons_options = implode("\n", $addons_lines);
                }
                $addons_min_chars = (string) ($addons_row['min_chars'] ?? '');
                $addons_max_chars = (string) ($addons_row['max_chars'] ?? '');
                ?>
                <div class="addons-admin-row" data-addons-row>
                    <p class="form-field">
                        <span class="addons-row-badge" aria-hidden="true"><?php esc_html_e('Option', 'addons'); ?></span>
                        <input type="text" name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][label]" placeholder="<?php esc_attr_e('Label (e.g. Gift wrap)', 'addons'); ?>" value="<?php echo esc_attr($addons_label); ?>" aria-label="<?php esc_attr_e('Option label', 'addons'); ?>" />
                        <select name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][type]" aria-label="<?php esc_attr_e('Option type', 'addons'); ?>">
                            <option value="text" <?php selected($addons_type, 'text'); ?>><?php esc_html_e('Text', 'addons'); ?></option>
                            <option value="checkbox" <?php selected($addons_type, 'checkbox'); ?>><?php esc_html_e('Checkbox', 'addons'); ?></option>
                            <option value="select" <?php selected($addons_type, 'select'); ?>><?php esc_html_e('Select', 'addons'); ?></option>
                        </select>
                        <input type="text" inputmode="decimal" name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][price]" placeholder="<?php esc_attr_e('Price', 'addons'); ?>" value="<?php echo esc_attr($addons_price); ?>" style="width:6em;" aria-label="<?php esc_attr_e('Extra price added when chosen', 'addons'); ?>" />
                        <span class="addons-char-settings" data-addons-char-settings <?php echo $addons_type === 'text' ? '' : 'style="display:none;"'; ?>>
                            <input type="number" min="0" name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][min_chars]" placeholder="<?php esc_attr_e('Min chars', 'addons'); ?>" value="<?php echo esc_attr($addons_min_chars); ?>" style="width:6em;" aria-label="<?php esc_attr_e('Minimum character length', 'addons'); ?>" />
                            <input type="number" min="0" name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][max_chars]" placeholder="<?php esc_attr_e('Max chars', 'addons'); ?>" value="<?php echo esc_attr($addons_max_chars); ?>" style="width:6em;" aria-label="<?php esc_attr_e('Maximum character length', 'addons'); ?>" />
                        </span>
                        <label>
                            <input type="checkbox" name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][required]" value="1" <?php checked($addons_required); ?> />
                            <?php esc_html_e('Required', 'addons'); ?>
                        </label>
                        <button type="button" class="button addons-admin-remove" data-addons-remove><?php esc_html_e('Remove', 'addons'); ?></button>
                    </p>
                    <p class="form-field">
                        <textarea name="addons_def[<?php echo esc_attr((string) $addons_i); ?>][options]" rows="3" placeholder="<?php esc_attr_e('Select options, one per line: Label | price', 'addons'); ?>" style="width:100%;" aria-label="<?php esc_attr_e('Choices for the Select type', 'addons'); ?>"><?php echo esc_textarea($addons_options); ?></textarea>
                        <span class="addons-options-help description"><?php esc_html_e('Only used for the "Select" type. One choice per line as "Label | price", e.g. "Standard | 0" and "Premium | 9.99". Omit the price for a free choice.', 'addons'); ?></span>
                    </p>
                    <?php
                    /**
                     * Render extra admin fields for one add-on definition row.
                     *
                     * Premium extensions use this hook to store extra metadata
                     * without replacing the free product-data template.
                     *
                     * @param int                  $addons_i   Row index.
                     * @param array<string, mixed> $addons_row Stored row data.
                     */
                    do_action('addons_product_data_row_fields', (int) $addons_i, $addons_row);
                    ?>
                </div>
            <?php endforeach; ?>
        </div>

        <p class="form-field">
            <button type="button" class="button button-primary addons-admin-add" data-addons-add><?php esc_html_e('Add option', 'addons'); ?></button>
        </p>

        <script type="text/template" id="addons-row-template">
            <div class="addons-admin-row" data-addons-row>
                <p class="form-field">
                    <span class="addons-row-badge" aria-hidden="true"><?php esc_html_e('Option', 'addons'); ?></span>
                    <input type="text" name="addons_def[__INDEX__][label]" placeholder="<?php esc_attr_e('Label (e.g. Gift wrap)', 'addons'); ?>" value="" aria-label="<?php esc_attr_e('Option label', 'addons'); ?>" />
                    <select name="addons_def[__INDEX__][type]" aria-label="<?php esc_attr_e('Option type', 'addons'); ?>">
                        <option value="text"><?php esc_html_e('Text', 'addons'); ?></option>
                        <option value="checkbox"><?php esc_html_e('Checkbox', 'addons'); ?></option>
                        <option value="select"><?php esc_html_e('Select', 'addons'); ?></option>
                    </select>
                    <input type="text" inputmode="decimal" name="addons_def[__INDEX__][price]" placeholder="<?php esc_attr_e('Price', 'addons'); ?>" value="" style="width:6em;" aria-label="<?php esc_attr_e('Extra price added when chosen', 'addons'); ?>" />
                    <span class="addons-char-settings" data-addons-char-settings>
                        <input type="number" min="0" name="addons_def[__INDEX__][min_chars]" placeholder="<?php esc_attr_e('Min chars', 'addons'); ?>" value="" style="width:6em;" aria-label="<?php esc_attr_e('Minimum character length', 'addons'); ?>" />
                        <input type="number" min="0" name="addons_def[__INDEX__][max_chars]" placeholder="<?php esc_attr_e('Max chars', 'addons'); ?>" value="" style="width:6em;" aria-label="<?php esc_attr_e('Maximum character length', 'addons'); ?>" />
                    </span>
                    <label>
                        <input type="checkbox" name="addons_def[__INDEX__][required]" value="1" />
                        <?php esc_html_e('Required', 'addons'); ?>
                    </label>
                    <button type="button" class="button addons-admin-remove" data-addons-remove><?php esc_html_e('Remove', 'addons'); ?></button>
                </p>
                <p class="form-field">
                    <textarea name="addons_def[__INDEX__][options]" rows="3" placeholder="<?php esc_attr_e('Select options, one per line: Label | price', 'addons'); ?>" style="width:100%;" aria-label="<?php esc_attr_e('Choices for the Select type', 'addons'); ?>"></textarea>
                    <span class="addons-options-help description"><?php esc_html_e('Only used for the "Select" type. One choice per line as "Label | price", e.g. "Standard | 0" and "Premium | 9.99". Omit the price for a free choice.', 'addons'); ?></span>
                </p>
                <?php
                /**
                 * Render extra admin fields inside the JavaScript row template.
                 *
                 * Extensions should use the __INDEX__ placeholder in generated
                 * input names so the free repeater can replace it.
                 */
                do_action('addons_product_data_row_template_fields');
                ?>
            </div>
        </script>
    </div>
</div>
<?php
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
