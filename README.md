# Modularity Navigation

Modularity Navigation ports focused navigation-module capabilities from
Municipio LTS to modern Municipio without restoring Municipio Extended as a
compatibility layer.

## Supported combinations

- Existing `mod-navigation` posts remain the source of truth.
- Existing `mod_navigation_format`, `mod_navigation_source`,
  `mod_navigation_menu`, `mod_navigation_items`, `mod_navigation_show_if_empty`,
  and `mod_navigation_empty_message` metadata is reused.
- Formats `grid`, `buttons`, and `list` can each render sources `menu`,
  `manual`, and `children`.
- Menu sources render only top-level items. Manual sources retain their saved
  order, links, targets, icons, and button variants.
- Child-page sources resolve the current Municipio page and retain WordPress
  `menu_order` while excluding unpublished pages and pages hidden from menus.
- The legacy theme mod `mod_navigation_grid_style=blocks` is reused and exposed
  under Municipios existing component-appearance panel.
- Button output uses Municipios current Button and Icon components. List output
  uses the current Link and Icon components inside semantic unordered-list
  markup.

Other LTS formats and sources are intentionally outside this release. Their
values remain in the database but render no output until separately ported.

## Installation

Install `municipio/wp-plugin-modularity-navigation` with Composer. Composer
Installers places it in `wp-content/plugins/modularity-navigation` through
`extra.installer-name`.

The plugin supports modern Municipio only and requires its bundled Modularity
implementation.

## Data migration

No write migration runs. The plugin keeps the LTS post type, field names,
repeater shape, menu slug, and theme-mod key intact, so imported content remains
editable and repeat activation is idempotent by design.

## Development

```console
composer format
composer test
composer lint
```
