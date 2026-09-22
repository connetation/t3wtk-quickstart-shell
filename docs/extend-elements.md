# Overwriting / Extending `conn/t3kit-elements`

`conn/t3kit-elements` is a Composer-installed extension (`vendor/conn/t3kit-elements/`) — don't edit it directly, those changes are lost on `composer update`. All project-specific customisation happens in `packages/t3kit-sitepackage/`, which is fully owned by this project (see the main [README](../README.md#sitepackage)).

## 1. Site Set dependency

`conn/t3kit-elements` must be listed as a dependency in `config/sites/sitepackage/config.yaml`:

```yaml
dependencies:
  - conn/t3kit-sitepackage
  - conn/t3kit-elements
```

Without this, its TypoScript/TCA never loads for the site.

## 2. Template overrides (Fluid)

`packages/t3kit-sitepackage/Configuration/Sets/Sitepackage/setup.typoscript` already declares override paths at index `50`, which are checked before `conn_t3kit_elements` (`40`) and `conn_t3kit_base` (`30`):

```typoscript
lib.contentElement {
    templateRootPaths.50 = EXT:conn_t3kit_sitepackage/Resources/Private/Templates/
    partialRootPaths.50  = EXT:conn_t3kit_sitepackage/Resources/Private/Partials/
    layoutRootPaths.50   = EXT:conn_t3kit_sitepackage/Resources/Private/Layouts/
}
```

To override a specific element's template, drop a same-named file at the matching path in `packages/t3kit-sitepackage/Resources/Private/Templates/ContentElements/<Name>.html` (and `Partials/<Name>/`, `Layouts/` as needed) — Fluid resolves the higher-index path first.

**Existing CTypes need an explicit path too.** `fluid_styled_content` and `conn_t3kit_elements` copy `lib.contentElement` into each `tt_content.<CType>` with `=<` *before* the sitepackage Set loads, so the generic path above only reaches content elements registered *after* it. To override an existing element's template, set the path directly on its CType:

```typoscript
tt_content.textmedia.templateRootPaths.50 = EXT:conn_t3kit_sitepackage/Resources/Private/Templates/
```

## 3. TCA overrides

Add/change fields on an existing element (or its child records) in `packages/t3kit-sitepackage/Configuration/TCA/Overrides/tt_content.php`. Follow the existing pattern in `vendor/conn/t3kit-elements/Configuration/TCA/Overrides/tt_content.php` for reference — copy the structure, don't edit the vendor file.

After changing TCA or `ext_tables.sql`: **Admin Tools → Maintenance → Analyze Database Structure** to apply schema changes.

## 4. SCSS

Element styles use `var(--conn-*)` design tokens defined in `conn/t3kit-base`. Override tokens or add component-specific overrides in `packages/t3kit-sitepackage/Resources/Private/Scss/_theme.scss`, then rebuild:

```bash
ddev exec "cd /var/www/html/packages/t3kit-sitepackage && npm run build"
```

## 5. PHP (Data Processors, ViewHelpers)

Extend or replace a `conn/t3kit-elements` Data Processor by pointing the element's TypoScript at your own class under `packages/t3kit-sitepackage/Classes/DataProcessing/` (PHP namespace `Connetation\T3kitSitepackage\`), rather than modifying the vendor class.

## Adding a brand-new element

Use the scaffold CLI instead of hand-writing all of the above — see [create-new-element.md](create-new-element.md).
