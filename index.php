<?php

/*
Plugin Name: Unique Headers
Plugin URI: https://geek.hellyer.kiwi/plugins/unique-headers/
Description: Unique Headers
Version: 2.1.1
Author: Ryan Hellyer
Author URI: https://geek.hellyer.kiwi/
License: GPLv2 or later

------------------------------------------------------------------------
Copyright Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/

declare(strict_types=1);

use RyanHellyer\UniqueHeaders\Vendor\Inpsyde\Modularity\Package;
use RyanHellyer\UniqueHeaders\Vendor\Inpsyde\Modularity\Properties\PluginProperties;
use RyanHellyer\UniqueHeaders\AdminModule;
use RyanHellyer\UniqueHeaders\AttachmentHelper;
use RyanHellyer\UniqueHeaders\DisplayModule;

$autoloader = __DIR__ . '/vendor/autoload.php';
if (! file_exists($autoloader)) {
    return;
}
require_once $autoloader;

// Fallback: if the Composer autoload runtime is missing (e.g. stripped from
// a production zip), register PSR-4 mappings for scoped dependencies directly.
if (! interface_exists('RyanHellyer\\UniqueHeaders\\Vendor\\Inpsyde\\Modularity\\Module\\Module')) {
    spl_autoload_register(static function (string $class): void {
        $prefixes = [
            'RyanHellyer\\UniqueHeaders\\Vendor\\Psr\\Container\\'
                => __DIR__ . '/vendor/psr/container/src/',
            'RyanHellyer\\UniqueHeaders\\Vendor\\Inpsyde\\Modularity\\'
                => __DIR__ . '/vendor/inpsyde/modularity/src/',
            'RyanHellyer\\UniqueHeaders\\'
                => __DIR__ . '/src/',
        ];
        foreach ($prefixes as $prefix => $baseDir) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                continue;
            }
            $file = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    });
}

$helper = new AttachmentHelper();

$properties = PluginProperties::new(__FILE__);
$package = Package::new($properties);
$package->addModule(new AdminModule($helper));
$package->addModule(new DisplayModule($helper));
$package->boot();
