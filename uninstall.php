<?php
/**
 * Uninstall cleanup for Add-Ons, Product Options for WooCommerce.
 *
 * Removes the plugin's own options. Per-product add-on definitions are stored as
 * standard product meta (`_addons_definitions`); they are intentionally left in
 * place so re-installing the plugin restores configured products, and so deleting
 * the plugin never silently rewrites the merchant's catalogue.
 *
 * @package Addons
 */

declare(strict_types=1);

// Exit if uninstall is not called from WordPress.
if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('addons_settings');
delete_option('addons_db_version');

// The PRO banner's dismissal is stored per user, so it belongs to the
// plugin rather than to the site content. User meta is global, not
// per-site, which is why this uses delete_metadata's \$delete_all rather
// than a loop over the users of one blog.
delete_metadata('user', 0, 'addons_pro_banner_dismissed', '', true);
