=== Add-Ons – Product Options for WooCommerce ===
Contributors: wppoland
Tags: woocommerce, product options, product addons, extra fields, custom product fields
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Requires Plugins: woocommerce
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add WooCommerce product options, product add-ons and custom product fields before add to cart.

== Description ==

Add-Ons lets store owners offer WooCommerce product options, product add-ons and custom product fields that customers pick before adding a product to the cart: gift wrapping, an engraving message, an extended warranty or a colour choice.

For each product you define a list of add-ons in the WooCommerce product editor. Every add-on has a label, a field type, an optional required flag, and an optional price.

* **Field types** — plain text, a checkbox, or a select drop-down.
* **Price deltas** — give an add-on (or each select option) a price; the amount is added to the cart line total automatically.
* **Free or paid** — leave the price at zero for free options such as a personalised message.
* **Cart & order display** — the customer's choices appear in the cart, at checkout, and on the order.
* **Display settings** — choose the group heading, show or hide option prices, toggle the required-field asterisk, and wrap the options in a bordered card — all from the Add-Ons settings page.

Add-on definitions are stored as standard product meta — no custom database tables — so the plugin itself stays small and fast.

Settings live under **WooCommerce → Add-Ons**. Removing the plugin cleans up its own options; your per-product definitions are kept as product meta so re-installing restores them.

The code is developed in the open at https://github.com/wppoland/addons — that's the place to report a bug or suggest a field type you'd like to see.

== Installation ==

1. Upload the plugin to `/wp-content/plugins/addons`, or install via Plugins → Add New.
2. Activate it. WooCommerce must be active.
3. Edit a product, open the **Add-Ons** tab in the Product data panel, and add your options.

== Frequently Asked Questions ==

= Does it require WooCommerce? =

Yes. WooCommerce must be installed and active.

= Where do customers see the options? =

On the single product page, just above the Add to cart button. Their selections then show in the cart, at checkout, and on the order.

= What field types are included? =

The free version includes text fields, checkboxes and select drop-downs. Each field can be free or add a price to the cart line.

= Can a product option change the price? =

Yes. Add a price to the row itself or to individual select choices, and Add-Ons adds that amount to the WooCommerce cart line.

= Can I make a product option required? =

Yes. Tick the Required checkbox for an option, and the product cannot be added to the cart until the shopper completes it.

= Does it create custom database tables? =

No. Add-on definitions are stored as product meta.

= Does it support file uploads or conditional logic? =

Those are PRO features. Add-Ons FREE focuses on fast text, checkbox and select product options.

== Screenshots ==

1. Add-Ons – Product Options for WooCommerce in action.

== External Services ==

Add-Ons does not connect to any external services. It sends no data off your site and loads no remote scripts, fonts or trackers — its admin and storefront CSS/JS are served from the plugin folder on your own server. Your add-on definitions are stored as product meta (`_addons_definitions`) and the display settings in a single option (`addons_settings`), all kept in your WordPress database.

== Changelog ==

= 0.2.0 =
* Add a customisable group heading shown above the add-on fields on the product page.
* Add display settings: show/hide option prices, toggle the required-field asterisk, and an optional bordered card style.
* Add an uninstall routine that removes the plugin's own options (product definitions are preserved as product meta).

= 0.1.0 =
* Initial release.
