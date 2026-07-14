<?php

/**
 * Plugin Name: Modularity Navigation
 * Description: Ports focused navigation-module capabilities to modern Municipio.
 * Version: 0.2.0
 * Author: Whitespace
 * License: MIT
 * Text Domain: modularity-navigation
 * Domain Path: /languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit();
}

define('MODULARITY_NAVIGATION_FILE', __FILE__);
/**
 * Modularity derives its asset context from module paths and only accepts paths below the
 * WordPress plugin directory. Composer's installer-name guarantees this location in production;
 * deriving it explicitly also keeps local symlink installations inside that runtime contract.
 */
define('MODULARITY_NAVIGATION_PATH', trailingslashit(WP_PLUGIN_DIR) . 'modularity-navigation/');
define('MODULARITY_NAVIGATION_URL', trailingslashit(plugins_url('modularity-navigation')));
define('MODULARITY_NAVIGATION_VERSION', '0.2.0');

$autoload = MODULARITY_NAVIGATION_PATH . 'vendor/autoload.php';

if (is_readable($autoload)) {
    require_once $autoload;
}

if (class_exists(\MunicipioModularityNavigation\Plugin::class)) {
    (new \MunicipioModularityNavigation\Plugin())->register();
}
