# Repository instructions

## Scope

This plugin ports focused Modularity navigation behavior to modern Municipio. Keep it independent
of Municipio Cloud and do not broaden a release to additional LTS formats or sources without a
separately confirmed outcome.

Preserve imported `mod-navigation` post types, `mod_navigation_*` metadata, and released theme-mod
keys unless an explicit, tested migration replaces them.

## Verification

Run these commands after changing PHP or runtime behavior:

```console
composer format
composer test
composer lint
```
