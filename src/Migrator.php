<?php

declare(strict_types=1);

namespace Addons;

defined('ABSPATH') || exit;

/**
 * Idempotent schema/version migrations, run on every boot. Compares a stored
 * option against VERSION and applies forward steps as needed.
 */
final class Migrator
{
    private const OPTION   = 'addons_db_version';
    private const SETTINGS = 'addons_settings';

    /**
     * The English strings that shipped as packaged defaults up to 1.0.8 and were
     * written into the option the first time an admin saved the settings screen.
     *
     * @var array<string, string>
     */
    private const LEGACY_TEXTS = [
        'group_title' => 'Product options',
    ];

    public function maybeMigrate(): void
    {
        $current = (string) get_option(self::OPTION, '0');

        if (version_compare($current, VERSION, '>=')) {
            return;
        }

        $this->clearUntranslatableTexts();

        update_option(self::OPTION, VERSION, false);
    }

    /**
     * Clear a stored heading that is byte for byte the old English default.
     *
     * That value could never be translated: it came from a config array, not a
     * gettext call, so a shop running in Polish printed "Product options" above
     * the add-on fields however complete the language pack was, and the settings
     * screen rendered it as the field value so the first save froze it in.
     * Empty now means "use the translated default" from
     * {@see \Addons\Service\Texts}.
     *
     * Only an exact match is cleared, so a merchant's own heading, including a
     * hand translation of the English one, survives untouched.
     *
     * The same pass carries over the one thing an empty value used to mean. Up
     * to 1.0.8 a merchant hid the heading by clearing the field, so a stored
     * empty string is a deliberate "no heading" and becomes
     * `show_group_title = false`. Without this they would silently get the
     * default heading back on the next page load.
     */
    private function clearUntranslatableTexts(): void
    {
        $stored = get_option(self::SETTINGS, null);

        if (! is_array($stored)) {
            return;
        }

        $changed = false;

        if (array_key_exists('group_title', $stored) && ! array_key_exists('show_group_title', $stored)) {
            $stored['show_group_title'] = trim((string) $stored['group_title']) !== '';
            $changed                    = true;
        }

        foreach (self::LEGACY_TEXTS as $key => $legacy) {
            if (isset($stored[$key]) && (string) $stored[$key] === $legacy) {
                $stored[$key] = '';
                $changed      = true;
            }
        }

        if ($changed) {
            // null keeps the option's existing autoload flag. Passing false here
            // would quietly move the settings out of the autoloaded set on every
            // shop that took this update, which is not a change a text sweep gets
            // to make.
            update_option(self::SETTINGS, $stored, null);
        }
    }
}
