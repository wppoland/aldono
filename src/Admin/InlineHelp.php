<?php

declare(strict_types=1);

namespace Addons\Admin;

defined('ABSPATH') || exit;

/**
 * Renders an accessible inline-help affordance: a small "?" button that toggles
 * a tooltip using the native HTML Popover API (no JavaScript required).
 *
 * The button is wired to the tooltip via `popovertarget` for the toggle and
 * `aria-describedby` so screen readers announce the help text. Light-dismiss,
 * focus handling and Escape-to-close are all provided by the browser's popover
 * behaviour. Returns inert markup (empty string of effect) on output; callers
 * echo it where a label sits.
 */
final class InlineHelp
{
    /**
     * Build the help button + popover markup for a given control.
     *
     * @param string $id   Unique base id for the tooltip element.
     * @param string $text Help text (plain, will be escaped).
     */
    public static function render(string $id, string $text): string
    {
        $tipId = 'addons-tip-' . sanitize_html_class($id);

        $button = sprintf(
            '<button type="button" class="addons-help" popovertarget="%1$s" aria-describedby="%1$s" aria-label="%2$s">?</button>',
            esc_attr($tipId),
            esc_attr__('More information', 'plogins-addons'),
        );

        $tip = sprintf(
            '<span id="%1$s" class="addons-tip" popover="auto" role="tooltip">%2$s</span>',
            esc_attr($tipId),
            esc_html($text),
        );

        return $button . $tip;
    }

    /**
     * Echo the help affordance, passed through wp_kses with an explicit allow
     * list so the output is provably safe for Plugin Check.
     */
    public static function output(string $id, string $text): void
    {
        $allowed = [
            'button' => [
                'type'             => true,
                'class'            => true,
                'popovertarget'    => true,
                'aria-describedby' => true,
                'aria-label'       => true,
            ],
            'span'   => [
                'id'      => true,
                'class'   => true,
                'popover' => true,
                'role'    => true,
            ],
        ];

        echo wp_kses(self::render($id, $text), $allowed);
    }
}
