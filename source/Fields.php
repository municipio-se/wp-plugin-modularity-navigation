<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation;

final class Fields
{
    public static function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_mod_navigation',
            'title' => _x('Navigation module', 'Navigation Module Field Group Title', 'modularity-navigation'),
            'fields' => self::definitions(),
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'mod-navigation',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Preserve the LTS field names and keys so imported module posts remain editable and
     * render without a destructive data migration.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function definitions(): array
    {
        return [
            [
                'key' => 'field_mod_navigation_format',
                'label' => _x('Format', 'Navigation Module Field Label', 'modularity-navigation'),
                'name' => 'mod_navigation_format',
                'type' => 'select',
                'required' => 1,
                'return_format' => 'value',
                'choices' => [
                    'grid' => _x('Grid', 'Navigation Module Format Choice', 'modularity-navigation'),
                    'buttons' => _x('Buttons', 'Navigation Module Format Choice', 'modularity-navigation'),
                    'list' => _x('List', 'Navigation Module Format Choice', 'modularity-navigation'),
                    'inline' => _x('Inline', 'Navigation Module Format Choice', 'modularity-navigation'),
                    'bar' => _x('Bar', 'Navigation Module Format Choice', 'modularity-navigation'),
                ],
            ],
            [
                'key' => 'field_mod_navigation_source',
                'label' => _x('Source', 'Navigation Module Field Label', 'modularity-navigation'),
                'name' => 'mod_navigation_source',
                'type' => 'select',
                'required' => 1,
                'return_format' => 'value',
                'choices' => [
                    'menu' => _x('Menu', 'Navigation Module Source Choice', 'modularity-navigation'),
                    'manual' => _x('Manually selected', 'Navigation Module Source Choice', 'modularity-navigation'),
                    'children' => _x('Child pages', 'Navigation Module Source Choice', 'modularity-navigation'),
                ],
            ],
            [
                'key' => 'field_mod_navigation_menu',
                'label' => _x('Menu', 'Navigation Module Field Label', 'modularity-navigation'),
                'name' => 'mod_navigation_menu',
                'type' => 'select',
                'return_format' => 'value',
                'choices' => self::menuChoices(),
                'allow_null' => 1,
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_mod_navigation_source',
                            'operator' => '==',
                            'value' => 'menu',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'field_mod_navigation_items',
                'label' => _x('Items', 'Navigation Module Field Label', 'modularity-navigation'),
                'name' => 'mod_navigation_items',
                'type' => 'repeater',
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_mod_navigation_source',
                            'operator' => '==',
                            'value' => 'manual',
                        ],
                    ],
                ],
                'sub_fields' => [
                    [
                        'key' => 'field_mod_navigation_item_link',
                        'label' => _x('Link', 'Navigation Module Field Label', 'modularity-navigation'),
                        'name' => 'link',
                        'type' => 'link',
                        'required' => 1,
                        'wrapper' => ['width' => '75%'],
                    ],
                    [
                        'key' => 'field_mod_navigation_button_variant',
                        'label' => _x('Variant', 'Navigation Module Field Label', 'modularity-navigation'),
                        'name' => 'button_variant',
                        'type' => 'select',
                        'choices' => [
                            'primary' => _x(
                                'Primary',
                                'Navigation Module Button Variant Choice',
                                'modularity-navigation',
                            ),
                            'secondary' => _x(
                                'Secondary',
                                'Navigation Module Button Variant Choice',
                                'modularity-navigation',
                            ),
                            'default' => _x(
                                'Default',
                                'Navigation Module Button Variant Choice',
                                'modularity-navigation',
                            ),
                        ],
                        'default_value' => 'default',
                        'wrapper' => ['width' => '25%'],
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_mod_navigation_format',
                                    'operator' => '==',
                                    'value' => 'buttons',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_mod_navigation_item_icon',
                        'label' => _x('Icon', 'Navigation Module Field Label', 'modularity-navigation'),
                        'name' => 'icon',
                        'type' => 'icon',
                        'wrapper' => ['width' => '25%'],
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_mod_navigation_format',
                                    'operator' => '==',
                                    'value' => 'buttons',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_mod_navigation_format',
                                    'operator' => '==',
                                    'value' => 'list',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_mod_navigation_format',
                                    'operator' => '==',
                                    'value' => 'inline',
                                ],
                            ],
                            [
                                [
                                    'field' => 'field_mod_navigation_format',
                                    'operator' => '==',
                                    'value' => 'bar',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'field_mod_navigation_show_if_empty',
                'label' => _x(
                    'Display this module even when there are no links',
                    'Navigation Module Field Label',
                    'modularity-navigation',
                ),
                'name' => 'mod_navigation_show_if_empty',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
            ],
            [
                'key' => 'field_mod_navigation_empty_message',
                'label' => _x(
                    'Message to display when there are no links',
                    'Navigation Module Field Label',
                    'modularity-navigation',
                ),
                'name' => 'mod_navigation_empty_message',
                'type' => 'wysiwyg',
                'default_value' => '',
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_mod_navigation_show_if_empty',
                            'operator' => '==',
                            'value' => '1',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function menuChoices(): array
    {
        if (!function_exists('wp_get_nav_menus')) {
            return [];
        }

        $choices = [];

        $menus = wp_get_nav_menus();

        foreach (is_array($menus) ? $menus : [] as $menu) {
            $slug = $menu->slug ?? null;
            $name = $menu->name ?? null;

            if (!is_string($slug) || !is_string($name)) {
                continue;
            }

            $choices[$slug] = $name;
        }

        return $choices;
    }
}
