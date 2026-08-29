# Modularity Navigation

Modularity Navigation ports focused navigation-module capabilities from
Municipio LTS to modern Municipio without restoring Municipio Extended as a
compatibility layer.

## Supported combinations

- Existing `mod-navigation` posts remain the source of truth.
- Existing `mod_navigation_format`, `mod_navigation_source`,
  `mod_navigation_menu`, `mod_navigation_items`, `mod_navigation_show_if_empty`,
  and `mod_navigation_empty_message` metadata is reused.
- Formats `grid`, `buttons`, `list`, `cards`, `inline`, and `bar` can render
  sources `menu`, `manual`, `children`, and `siblings`.
- Menu sources render only top-level items. Manual sources retain their saved
  order, links, targets, icons, and button variants.
- Child-page sources resolve the current Municipio page and retain WordPress
  `menu_order` while excluding unpublished pages and pages hidden from menus.
- The legacy theme mod `mod_navigation_grid_style=blocks` is reused and exposed
  under Municipios existing component-appearance panel.
- Button output uses Municipios current Button and Icon components. List output
  uses the current Link and Icon components inside semantic unordered-list
  markup.
- Card output uses Municipios current Card component and keeps Navigation's
  saved title, URL, target, source order, and empty-state behavior. A link to a
  local post reuses Municipios post-object contract for its featured image and
  filtered short excerpt. External or unresolved links receive no invented
  excerpt and use the Card component's own placeholder behavior.
- Card columns follow the module's available width. Each column is at least
  `18rem` when space allows, and an incomplete final row keeps empty column
  tracks so its cards remain the same width as the preceding row.

The plugin does not copy LTS templates, utility classes, colors, pixel sizes,
or its former eight-word card excerpt. It requires modern Municipio and its
Card and post-object contracts.

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

## Grid presentation extension

The package-owned `navigation-grid` Blade view remains the default. A site
plugin can provide a focused grid presentation without replacing the complete
Navigation module by registering its view root through
`/Modularity/externalViewPath` and returning a plain Blade view name from:

```php
add_filter(
    'MunicipioModularityNavigation/gridPresentationView',
    static fn(): string => 'site-navigation-grid',
);
```

The filter also receives the module ID, normalized items, and current grid style
as its second through fourth arguments. The selected view receives Navigation's
normalized module data. Invalid or missing override views safely fall back to
`navigation-grid`; buttons, list, inline, bar, module title, and empty-state
rendering never use the override.

Site presentations can set `--mod-navigation-grid-link-padding` and
`--mod-navigation-grid-focus-color` on their own grid wrapper or items. Both
variables fall back to the package's existing spacing and primary focus color,
so the shared presentation is unchanged.

## Development

```console
composer format
composer test
composer lint
```
