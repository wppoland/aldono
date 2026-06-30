<?php

declare(strict_types=1);

namespace Addons\Admin;

use Addons\Contract\HasHooks;
use Addons\Service\AddOnsService;

defined('ABSPATH') || exit;

// InlineHelp is used in renderPage() for accessible "?" tooltips.

/**
 * Admin settings page registered as a WooCommerce submenu.
 *
 * Stores the master toggle in the `addons_settings` option (array). Per-product
 * add-on definitions are edited in the product "Add-Ons" data tab, not here.
 * All output is escaped; all input is sanitised on save.
 */
final class Settings implements HasHooks
{
    private const PAGE = 'addons-settings';

    /**
     * Settings page hook suffix, captured so assets load on this screen only.
     */
    private string $pageHook = '';

    public function registerHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function addMenuPage(): void
    {
        $hook = add_submenu_page(
            'woocommerce',
            __('Add-Ons', 'plogins-addons'),
            __('Add-Ons', 'plogins-addons'),
            'manage_woocommerce',
            self::PAGE,
            [$this, 'renderPage'],
        );

        $this->pageHook = is_string($hook) ? $hook : '';
    }

    /**
     * Load the admin stylesheet on the Add-Ons settings screen only.
     */
    public function enqueueAssets(string $hook): void
    {
        if ($this->pageHook === '' || $hook !== $this->pageHook) {
            return;
        }

        wp_enqueue_style(
            'addons-admin',
            \Addons\Plugin::instance()->url('assets/css/admin.css'),
            [],
            \Addons\VERSION,
        );
    }

    public function registerSettings(): void
    {
        register_setting(
            self::PAGE,
            AddOnsService::OPTION,
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize'],
            ],
        );

        // The menu uses manage_woocommerce; align the options.php save capability
        // so shop managers (not just admins with manage_options) can save.
        add_filter(
            'option_page_capability_' . self::PAGE,
            static fn (): string => 'manage_woocommerce',
        );
    }

    public function renderPage(): void
    {
        if (! current_user_can('manage_woocommerce')) {
            return;
        }

        $settings = $this->settings();
        $option   = AddOnsService::OPTION;
        ?>
        <div class="wrap addons-admin addons-settings">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <p class="description addons-settings__lead">
                <?php esc_html_e('These options control how product add-ons look and behave across your whole store. To choose which options appear on a specific product, edit that product and open the "Add-Ons" tab in the Product data box.', 'plogins-addons'); ?>
            </p>

            <form method="post" action="options.php">
                <?php settings_fields(self::PAGE); ?>

                <h2 class="addons-settings__section"><?php esc_html_e('Status', 'plogins-addons'); ?></h2>
                <p class="description addons-settings__section-intro">
                    <?php esc_html_e('Switch the whole feature on or off without touching any product\'s configured options.', 'plogins-addons'); ?>
                </p>

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <?php esc_html_e('Enable add-ons', 'plogins-addons'); ?>
                                <?php InlineHelp::output('enabled', __('The master switch. When off, no add-on fields are shown on any product and no price changes are applied in the cart, even if products still have add-ons configured. Turn it on once you are ready to go live.', 'plogins-addons')); ?>
                            </th>
                            <td>
                                <label for="addons_enabled">
                                    <input
                                        type="checkbox"
                                        id="addons_enabled"
                                        name="<?php echo esc_attr($option); ?>[enabled]"
                                        value="1"
                                        <?php checked((bool) ($settings['enabled'] ?? false), true); ?>
                                    />
                                    <?php esc_html_e('Show per-product add-on fields on the product page and apply price deltas in the cart.', 'plogins-addons'); ?>
                                </label>
                                <p class="description">
                                    <?php esc_html_e('Leave this on for normal operation. Switch it off to temporarily hide all add-ons without deleting any product settings.', 'plogins-addons'); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h2 class="addons-settings__section"><?php esc_html_e('On the product page', 'plogins-addons'); ?></h2>
                <p class="description addons-settings__section-intro">
                    <?php esc_html_e('How the add-on group is labelled and styled where customers see it. These choices affect appearance only, not pricing or which options show.', 'plogins-addons'); ?>
                </p>

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="addons_group_title"><?php esc_html_e('Group heading', 'plogins-addons'); ?></label>
                                <?php InlineHelp::output('group_title', __('A short heading printed above the options on the product page, for example "Personalise your order" or "Extras". It helps customers understand that the fields are optional add-ons. Clear the field to show no heading at all.', 'plogins-addons')); ?>
                            </th>
                            <td>
                                <input
                                    type="text"
                                    id="addons_group_title"
                                    class="regular-text"
                                    name="<?php echo esc_attr($option); ?>[group_title]"
                                    value="<?php echo esc_attr((string) ($settings['group_title'] ?? '')); ?>"
                                    placeholder="<?php esc_attr_e('e.g. Product options', 'plogins-addons'); ?>"
                                />
                                <p class="description">
                                    <?php
                                    printf(
                                        /* translators: %s: the packaged default group heading, e.g. "Product options". */
                                        esc_html__('Heading shown above the fields on the product page. Leave empty to hide it. Default: %s.', 'plogins-addons'),
                                        '<code>' . esc_html((string) ($this->defaults()['group_title'] ?? '')) . '</code>',
                                    );
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php esc_html_e('Display', 'plogins-addons'); ?>
                                <?php InlineHelp::output('display', __('Presentation choices that change how the add-ons look on the storefront. They do not affect pricing or which options appear, only the visual treatment.', 'plogins-addons')); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php esc_html_e('Display', 'plogins-addons'); ?></span>
                                    </legend>
                                    <label for="addons_show_prices">
                                        <input
                                            type="checkbox"
                                            id="addons_show_prices"
                                            name="<?php echo esc_attr($option); ?>[show_prices]"
                                            value="1"
                                            <?php checked((bool) ($settings['show_prices'] ?? false), true); ?>
                                        />
                                        <?php esc_html_e('Show the price next to paid options.', 'plogins-addons'); ?>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e('Displays the extra cost in brackets, e.g. "Gift wrap (+$5.00)". Free options never show a price.', 'plogins-addons'); ?>
                                    </p>
                                    <br />
                                    <label for="addons_show_required">
                                        <input
                                            type="checkbox"
                                            id="addons_show_required"
                                            name="<?php echo esc_attr($option); ?>[show_required]"
                                            value="1"
                                            <?php checked((bool) ($settings['show_required'] ?? false), true); ?>
                                        />
                                        <?php esc_html_e('Mark required fields with an asterisk.', 'plogins-addons'); ?>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e('Adds a red * after the label of any option a customer must complete before adding to cart. Required options are still enforced even with this off.', 'plogins-addons'); ?>
                                    </p>
                                    <br />
                                    <label for="addons_card_style">
                                        <input
                                            type="checkbox"
                                            id="addons_card_style"
                                            name="<?php echo esc_attr($option); ?>[card_style]"
                                            value="1"
                                            <?php checked((bool) ($settings['card_style'] ?? false), true); ?>
                                        />
                                        <?php esc_html_e('Wrap the options in a bordered card.', 'plogins-addons'); ?>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e('Groups the options inside a subtle bordered box so they stand out from the rest of the product page. Turn off for a plain, inline layout that inherits your theme.', 'plogins-addons'); ?>
                                    </p>
                                </fieldset>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="description addons-settings__next">
                    <span class="addons-settings__next-label"><?php esc_html_e('Next step', 'plogins-addons'); ?></span>
                    <?php esc_html_e('Define each product\'s add-ons in the product editor, under the "Add-Ons" tab in the Product data panel.', 'plogins-addons'); ?>
                </p>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Sanitise the submitted settings before save.
     *
     * @param mixed $raw
     * @return array<string, mixed>
     */
    public function sanitize(mixed $raw): array
    {
        if (! is_array($raw)) {
            $raw = [];
        }

        $defaults = $this->settings();

        return array_merge($defaults, [
            'enabled'       => ! empty($raw['enabled']),
            'group_title'   => isset($raw['group_title']) ? sanitize_text_field((string) $raw['group_title']) : '',
            'show_prices'   => ! empty($raw['show_prices']),
            'show_required' => ! empty($raw['show_required']),
            'card_style'    => ! empty($raw['card_style']),
        ]);
    }

    /**
     * Stored settings merged over packaged defaults.
     *
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        $stored = get_option(AddOnsService::OPTION, []);

        if (! is_array($stored)) {
            $stored = [];
        }

        return array_merge($this->defaults(), $stored);
    }

    /**
     * Packaged defaults, used for display hints and as the merge base.
     *
     * @return array<string, mixed>
     */
    private function defaults(): array
    {
        /** @var array<string, mixed> $defaults */
        $defaults = require ADDONS_DIR . 'config/defaults.php';

        return $defaults;
    }
}
