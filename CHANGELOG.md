# Changelog

## Unreleased — 1.1.0

Restore the Tree format with Standard and Highlighted presentations and the
existing site-wide LTS setting. Menus and child pages show two levels; manual
selections and siblings remain flat. Preserve optional featured images,
navigation descriptions and saved module data without a write migration.

## 1.0.2 — 2026-10-13

Reduce child-navigation query cost by filtering visibility with bulk-loaded post
metadata instead of metadata joins. Preserve child scope, ascending menu order
and visibility for missing and repeated metadata values. No data migration is
required.

## 1.0.1 — 2026-10-13

Reduce sibling-navigation query cost by filtering visibility with bulk-loaded
post metadata instead of metadata joins. Preserve sibling scope, ordering and
visibility for missing and repeated metadata values. No data migration is
required.

## 1.0.0 — 2026-09-17

First stable release with independent semantic versioning.

Navigation sources and formats documented in README, responsive cards, the grid
presentation extension, and the module.navigation.cards context for quality
integrations.

The runtime behavior is preserved from the reviewed development baseline; this
preparation aligns release metadata and documents the release contract.
Reference acceptance and published Composer distribution were verified before
consumer adoption.

Documentation status corrected after publication. The immutable 1.0.0 archive
retains the original candidate wording; runtime code and the tag are unchanged.
