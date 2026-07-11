<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

use MunicipioModularityNavigation\Customizer\GridSettings;

final class Plugin
{
    public function register(): void
    {
        add_action('init', [$this, 'loadTextDomain'], -10);
        add_action('init', [$this, 'registerModule'], 1);
        add_action('acf/init', [Fields::class, 'register']);
        add_filter('/Modularity/externalViewPath', [$this, 'registerViewPath']);
        add_action('municipio_customizer_panel_registered', [new GridSettings(), 'register'], 10, 1);
    }

    /**
     * Load bundled translations before Modularity creates translated module labels. The fixed
     * installer directory is also the production contract used for this plugin's symlink-safe
     * assets and views.
     */
    public function loadTextDomain(): void
    {
        load_plugin_textdomain('modularity-navigation', false, 'modularity-navigation/languages');
    }

    /**
     * Modularity builds its module registry at init priority 10. Registering at priority 1
     * keeps this plugin independent of theme load order while making the module available
     * before that registry is finalized.
     */
    public function registerModule(): void
    {
        if (!function_exists('modularity_register_module')) {
            return;
        }

        modularity_register_module(MODULARITY_NAVIGATION_PATH . 'source/Module', 'Navigation');
    }

    /**
     * Municipio 6 first resolves the file through the module's templateDir and then gives the
     * Blade engine this post-type-specific view root. Both extension points are required.
     *
     * @param array<string, string> $paths
     * @return array<string, string>
     */
    public function registerViewPath(array $paths): array
    {
        $paths['mod-navigation'] = MODULARITY_NAVIGATION_PATH . 'views';

        return $paths;
    }
}
