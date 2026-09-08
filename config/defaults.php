<?php
/**
 * Default settings, merged under the option key `addons_settings`.
 *
 * @package Addons
 *
 * @return array<string, mixed>
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

return [
    // Master switch: render fields and apply price deltas.
    'enabled'          => true,
    // Print the heading above the add-on fields at all. Until 1.0.9 this was
    // expressed by clearing `group_title`, which collided with the fix below.
    'show_group_title' => true,
    // The heading itself, empty on purpose. A string here is not a gettext call,
    // so it never reaches the .pot and no translator can reach it; the English
    // "Product options" used to print on every site and froze into the option on
    // the first settings save. Empty means "use Addons\Service\Texts", which is
    // translated. Anything the merchant types still wins.
    'group_title'      => '',

    // Show the formatted price next to paid options.
    'show_prices'      => true,
    // Show a red asterisk next to required field labels.
    'show_required'    => true,
    // Wrap the group in a styled card on the product page.
    'card_style'       => true,
];
