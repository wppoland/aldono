<?php

declare(strict_types=1);

namespace Addons\Service;

defined('ABSPATH') || exit;

/**
 * The customer-facing strings a merchant may override, in the language of the
 * site.
 *
 * The group heading used to be an English sentence in config/defaults.php. A
 * string in a config array is never wrapped in a gettext call, so it never
 * reached the .pot and no translator could touch it: a shop running in Polish
 * printed "Product options" above the add-on fields however complete the
 * language pack was. Worse, the settings screen rendered that same English as
 * the field value, so the first save froze it into `addons_settings`.
 *
 * The packaged default is now empty, meaning "use the string below". A merchant
 * who types their own still wins, and what they typed is stored as typed.
 * Hiding the heading altogether, which used to be spelled "clear this field",
 * now has its own `show_group_title` switch, so an empty value no longer has to
 * carry two meanings.
 */
final class Texts
{
    /**
     * Setting key => the translated default.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'group_title' => __('Product options', 'plogins-addons'),
        ];
    }

    /**
     * Fill every empty text key with its translated default.
     *
     * Applied on the way OUT, where the string is about to be shown, and never
     * on the way in: writing the resolved text back to the option would freeze
     * one language into the database, which is the bug this class exists to fix.
     *
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    public static function apply(array $settings): array
    {
        foreach (self::defaults() as $key => $text) {
            if (trim((string) ($settings[$key] ?? '')) === '') {
                $settings[$key] = $text;
            }
        }

        return $settings;
    }
}
