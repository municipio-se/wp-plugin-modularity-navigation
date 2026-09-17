# Release process

## Released 1.0.0

Version
[1.0.0](https://github.com/municipio-se/wp-plugin-modularity-navigation/releases/tag/1.0.0)
was published on 2026-09-17. Composer derives the version from the immutable Git
tag; do not add a version field to composer.json.

Use independent SemVer for the documented settings, stored data, integration
points and behavior. Backward-incompatible changes require a major release,
compatible additions a minor release and compatible fixes a patch release.

## Compatibility and acceptance

The current integration target is WordPress 6.9.4, PHP 8.3 and the bundled
Modularity in Municipio theme 6.43.3. Release acceptance covers the documented
reference cases; it does not prove every source, format or platform combination.
Preserve any earlier verified contracts described in README. The PHP ^8.2
Composer requirement does not mean every permitted platform combination has been
tested. Record exact dependencies and required upstream patches with the
acceptance evidence before extending support.

Verify each used source/format combination, saved order and targets, empty
states, grid overrides and cards with quality integrations both enabled and
disabled. The new card context must be tested with the installed Quality
Extensions version.

The pre-release suite passed on PHP 8.3.33 (44 tests / 198 assertions) on
2026-09-14. These are isolated tests and do not replace WordPress reference
acceptance. Existing lint diagnostics must be reviewed; a successful lint exit
is not a claim of zero warnings.

## Checklist for future releases

1. Review the final commit, changelog, plugin header and any asset-version
   constants. Run composer validate --strict, composer format, composer test and
   composer lint. Review formatting changes before committing.
2. Verify a fresh Composer installation and the supported upgrade paths in a
   named reference environment. Check shipped source, views, translations and
   assets where present; preserve saved content and unrelated behavior.
3. Obtain approval for the exact release commit and compatibility evidence. Only
   then publish an annotated version tag and matching GitHub release. Never move
   a published tag; corrections require a new version.
4. Verify Packagist indexes the release and that Composer source and dist
   references equal the tag's peeled commit. If indexing fails, investigate the
   existing integration before requesting a manual update. Do not treat local
   candidate installation as proof of published distribution.
5. In each approved consumer, require
   municipio/wp-plugin-modularity-navigation:^1.0, review the dependency diff,
   commit the lockfile, deploy after approval and repeat the relevant reference
   checks. Retain the previous lockfile and deployment reference for rollback.

## Rollback

No write migration runs. Roll back the package and lockfile; existing post
types, field keys, repeater data and theme mods remain intact.

## Publication evidence, 2026-09-17

The published tag was verified against Composer source/dist references and a
clean installation. Nora stage and production adopted 1.0.0 with WordPress
6.9.4, PHP 8.3.6 and Municipio 6.43.3. Acceptance is limited to the documented
reference cases and preserved configuration.

This documentation was corrected after publication. The immutable 1.0.0 archive
retains the original candidate wording. Never move the tag to include this
documentation correction.
