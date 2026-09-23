=== Aldono - Product Options for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, product options, product addons, extra fields, custom product fields
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.1
Requires Plugins: woocommerce
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add WooCommerce product options, product add-ons and custom product fields before add to cart.

== Description ==

Aldono lets store owners offer WooCommerce product options, product add-ons and custom product fields that customers pick before adding a product to the cart: gift wrapping, an engraving message, an extended warranty or a colour choice.

For each product you define a list of add-ons in the WooCommerce product editor. Every add-on has a label, a field type, an optional required flag, and an optional price.

* **Field types**, plain text, a checkbox, or a select drop-down.
* **Text limits**, set minimum and maximum character lengths for text product options.
* **Price deltas**, give an add-on (or each select option) a price; the amount is added to the cart line total automatically.
* **Free or paid**, leave the price at zero for free options such as a personalised message.
* **Cart & order display**, the customer's choices appear in the cart, at checkout, and on the order.
* **Display settings**, choose the group heading, show or hide option prices, toggle the required-field asterisk, and wrap the options in a bordered card, all from the Add-Ons settings page.

Add-on definitions are stored as standard product meta, no custom database tables, so the plugin itself stays small and fast.

Settings live under **WooCommerce > Add-Ons**. Removing the plugin cleans up its own options; your per-product definitions are kept as product meta so re-installing restores them.

The code is developed in the open at [github.com/wppoland/plogins-addons](https://github.com/wppoland/plogins-addons), that's the place to report a bug or suggest a field type you'd like to see.

== Installation ==

1. Upload the plugin to `/wp-content/plugins/aldono`, or install via Plugins > Add New.
2. Activate it. WooCommerce must be active.
3. Edit a product, open the **Add-Ons** tab in the Product data panel, and add your options.

== Frequently Asked Questions ==

= Documentation and links =

* **Documentation**: [plogins.com/plogins-addons/docs/](https://plogins.com/plogins-addons/docs/)
* **Plugin page**: [plogins.com/plogins-addons/](https://plogins.com/plogins-addons/)
* **Source code**: [github.com/wppoland/plogins-addons](https://github.com/wppoland/plogins-addons)
* **Bug reports and feature requests**: [github.com/wppoland/plogins-addons/issues](https://github.com/wppoland/plogins-addons/issues)


= Does it require WooCommerce? =

Yes. WooCommerce must be installed and active.

= Where do customers see the options? =

On the single product page, just above the Add to cart button. Their selections then show in the cart, at checkout, and on the order.

= What field types are included? =

The free version includes text fields, checkboxes and select drop-downs. Each field can be free or add a price to the cart line.

= Can a product option change the price? =

Yes. Add a price to the row itself or to individual select choices, and Aldono adds that amount to the WooCommerce cart line.

= Can I make a product option required? =

Yes. Tick the Required checkbox for an option, and the product cannot be added to the cart until the shopper completes it.

= Can I limit text option length? =

Yes. Text add-ons can have minimum and maximum character limits. The storefront shows a live counter and the server validates the same limits before add to cart.

= Does it create custom database tables? =

No. Add-on definitions are stored as product meta.

= Does it support file uploads or conditional logic? =

Those are PRO features. Aldono FREE focuses on fast text, checkbox and select product options.


= Does this plugin work on WordPress Multisite? =

Yes. This plugin is compatible with WordPress Multisite. Network activate it or activate it on individual sites; each site keeps its own settings and data.

== Screenshots ==

1. On the storefront.
2. Settings in the WordPress admin.
3. On a mobile device.
== External Services ==

Aldono does not connect to any external services. It sends no data off your site and loads no remote scripts, fonts or trackers, its admin and storefront CSS/JS are served from the plugin folder on your own server. Your add-on definitions are stored as product meta (`_addons_definitions`) and the display settings in a single option (`addons_settings`), all kept in your WordPress database.

== Translations ==

Aldono is fully translatable and ships the `aldono.pot` template. Translations are delivered by WordPress.org language packs from translate.wordpress.org, which is where Polish, German and Spanish are being contributed; the package itself carries no compiled translation files.

== Changelog ==

= 1.1.1 =
* The sidebar upgrade promo now follows the same dismissal as the banner. Dismissing the banner used to leave a full-height advert on the settings screen for good, which is not what the WordPress.org guideline on upgrade prompts means by used with moderation.

= 1.1.0 =
* Renamed to Aldono. The WordPress.org review team asks a plugin name to lead with a distinctive, coined identifier rather than a generic descriptive word. Aldono is Esperanto for an addition. The text domain follows the name; the stored add-on definitions, the settings and every hook are unchanged.

= 1.0.11 =
* Fixed: the PRO upgrade promo kept selling to people who had already bought the paid edition. Only the banner could be dismissed, so the sidebar promo and the locked feature cards followed a paying customer around for good. The promo now checks whether the paid edition is active and steps aside when it is.
* Fixed: arrow glyphs in the admin menu paths, and in the strings handed to translators. An arrow inside a translatable string makes the glyph every translator's problem and changes the layout in any locale that drops it.

= 1.0.10 =
* Fixed: deleting the plugin left the per-user "dismiss" flag from the PRO notice in the database. Uninstall now removes it for every user, not just the one who dismissed it.

= 1.0.9 =
* Fixed the heading above the product options always printing in English. "Product options" was a packaged default rather than a translatable string, so it never reached the translation file and a shop running in Polish, German or Spanish still showed English above the fields. It is now translated with the rest of the plugin, so it follows the site language as soon as a translation for it exists. Translations are delivered by WordPress.org language packs, not bundled in this download, so on a site with no pack for this plugin the heading stays English until one is published.
* Your own heading is untouched. Only a heading still set to the exact English default is reset so the translation can take over.
* Added a "Show the heading above the options" switch. Hiding the heading used to mean clearing the field, which now means "use the default"; if you had cleared it, the switch is set to off for you on update, so nothing changes on your product pages.

= 1.0.8 =
* Renamed to Plogins Add-Ons - Product Options for WooCommerce so the name leads with the brand rather than a generic word, which is what the WordPress.org plugin review team asks for. The plugin slug is unchanged.

= 1.0.7 =
* Tested against WordPress 7.1. Verified by activating this build on a clean 7.1 install with WooCommerce 11.1, not by editing the header.

= 1.0.6 =
* Fixed the PRO promo on the settings screen quoting a price in PLN. PRO is priced and charged in EUR, so an admin on a Polish site was shown a zloty amount and then billed in euro, and the zloty figure was a fixed conversion that drifted from the real charge as the rate moved. The promo now shows the euro price that is actually taken.

= 1.0.4 =
* Translations: completed Polish, German and Spanish for the PRO upgrade panel.

= 1.0.3 =
* Accessibility improvements to the admin and storefront markup.
* Fixed low-contrast admin headings under an OS dark-mode preference.

= 1.0.2 =
* Added bundled Polish, German and Spanish translations for the plugin interface.

= 1.0.1 =
* First stable release.

= 0.3.1 =
* Renamed to Plogins Add-Ons for WooCommerce for a more distinctive plugin name.

= 0.3.0 =
* Add minimum and maximum character limits for text add-ons, with storefront counters and server-side validation.

= 0.2.0 =
* Add a customisable group heading shown above the add-on fields on the product page.
* Add display settings: show/hide option prices, toggle the required-field asterisk, and an optional bordered card style.
* Add an uninstall routine that removes the plugin's own options (product definitions are preserved as product meta).

= 0.1.0 =
* Initial release.
