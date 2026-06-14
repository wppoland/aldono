<?php
/**
 * Service wiring. Returns a closure that registers every service in the
 * container.
 *
 * @package Addons
 */

declare(strict_types=1);

use Addons\Admin\ProductData;
use Addons\Admin\Settings;
use Addons\Container;
use Addons\Migrator;
use Addons\Service\AddOnsService;

defined('ABSPATH') || exit;

return static function (Container $c): void {
    $c->singleton(Migrator::class, static fn (): Migrator => new Migrator());

    $c->singleton(AddOnsService::class, static fn (): AddOnsService => new AddOnsService());

    // Admin (only needed in wp-admin context).
    if (is_admin()) {
        $c->singleton(ProductData::class, static fn (): ProductData => new ProductData());
        $c->singleton(Settings::class, static fn (): Settings => new Settings());
    }
};
