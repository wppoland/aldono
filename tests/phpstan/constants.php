<?php
/**
 * Constants needed by PHPStan to analyse the plugin without bootstrapping WordPress.
 *
 * @package Addons
 */

declare(strict_types=1);

namespace {
    if (! defined('ABSPATH')) {
        define('ABSPATH', '/tmp/wordpress/');
    }
    if (! defined('ADDONS_DIR')) {
        define('ADDONS_DIR', '/tmp/addons/');
    }
    if (! defined('ADDONS_URL')) {
        define('ADDONS_URL', 'https://example.test/wp-content/plugins/addons/');
    }
}

namespace Addons {
    if (! defined('Addons\\VERSION')) {
        define('Addons\\VERSION', '0.2.0');
    }
    if (! defined('Addons\\PLUGIN_FILE')) {
        define('Addons\\PLUGIN_FILE', '/tmp/addons/addons.php');
    }
}
