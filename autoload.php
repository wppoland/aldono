<?php
/**
 * Autoloading: prefer Composer's vendor autoloader (ships the storefront-kit and
 * the optimized classmap). Fall back to a minimal PSR-4 autoloader so the plugin
 * still boots if vendor/ is somehow absent.
 *
 * @package Addons
 */

declare(strict_types=1);

namespace Addons;

defined('ABSPATH') || exit;

$addons_composer = __DIR__ . '/vendor/autoload.php';
if (is_readable($addons_composer)) {
    require_once $addons_composer;
    return;
}

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'Addons\\'           => __DIR__ . '/src/',
        'WPPoland\\StorefrontKit\\'    => __DIR__ . '/lib/storefront-kit/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $relative = substr($class, $len);
        $file     = $baseDir . str_replace('\\', '/', $relative) . '.php';
        if (is_readable($file)) {
            require_once $file;
        }
        return;
    }
});
