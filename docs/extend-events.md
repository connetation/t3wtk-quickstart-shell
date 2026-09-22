# Overwriting / Extending `conn/t3kit-events`

> **Not yet part of this template.** `conn/t3kit-events` isn't in `composer.json` or `vendor/conn/` at the time of writing. This doc describes it based on how the equivalent Connetation package `conn/t3kit-base` wraps its upstream dependency — the same shape applies here once `conn/t3kit-events` is added. Update the specifics below once the package actually exists in `vendor/conn/t3kit-events/`.

`conn/t3kit-events` is a Connetation **Site Set** that requires and overrides [`derhansen/sf_event_mgt`](https://packagist.org/packages/derhansen/sf_event_mgt) (extension key `sf_event_mgt`) — same relationship as `conn/t3kit-base` has to `typo3/fluid-styled-content`: it pulls in the upstream extension as a Composer dependency, declares it as a Set dependency, and ships its own TypoScript/TCA/Fluid templates on top so event output matches the project's Bootstrap 5 theme out of the box.

Don't edit `vendor/conn/t3kit-events/` or `vendor/derhansen/sf_event_mgt/` directly — project-specific customisation happens in `packages/t3kit-sitepackage/`, which is fully owned by this project (see the main [README](../README.md#sitepackage)).

## 1. Composer + Site Set dependency

```bash
composer require conn/t3kit-events
```

`conn/t3kit-events`'s own `composer.json` requires `derhansen/sf_event_mgt`, so it comes along automatically. Add `conn/t3kit-events` (not `derhansen/sf_event_mgt`) to `config/sites/sitepackage/config.yaml` — its Set declares the dependency on the `sf_event_mgt` Set, cascading it in:

```yaml
dependencies:
  - conn/t3kit-sitepackage
  - conn/t3kit-elements
  - conn/t3kit-events
```

## 2. Template overrides (Fluid)

`sf_event_mgt` resolves templates via array-based paths under `plugin.tx_sfeventmgt.view`:

```typoscript
plugin.tx_sfeventmgt {
    view {
        templateRootPaths.20 = EXT:conn_t3kit_events/Resources/Private/Templates/
        partialRootPaths.20  = EXT:conn_t3kit_events/Resources/Private/Partials/
        layoutRootPaths.20   = EXT:conn_t3kit_events/Resources/Private/Layouts/
    }
}
```

`conn/t3kit-events` already adds its own index (e.g. `20`) on top of `sf_event_mgt`'s defaults. To override a template from the sitepackage, add a higher index in `packages/t3kit-sitepackage/Configuration/Sets/Sitepackage/setup.typoscript`, same pattern as the `lib.contentElement` override for `conn/t3kit-elements` (see [extend-elements.md](extend-elements.md#2-template-overrides-fluid)):

```typoscript
plugin.tx_sfeventmgt.view {
    templateRootPaths.50 = EXT:conn_t3kit_sitepackage/Resources/Private/Extensions/SfEventMgt/Templates/
    partialRootPaths.50  = EXT:conn_t3kit_sitepackage/Resources/Private/Extensions/SfEventMgt/Partials/
    layoutRootPaths.50   = EXT:conn_t3kit_sitepackage/Resources/Private/Extensions/SfEventMgt/Layouts/
}
```

Higher index wins, and only the specific file you place there is overridden — everything else still resolves from `conn/t3kit-events`.

Note: since TYPO3 12.4, `sf_event_mgt`'s **backend** template overrides (event administration module) use TSConfig rather than TypoScript, e.g. `templates.derhansen/sf_event_mgt.20 = EXT:conn_t3kit_sitepackage/Resources/Private/...` — only frontend plugin templates use the `plugin.tx_sfeventmgt.view` TypoScript shown above.

## 3. TCA overrides

Extend the event domain model (table `tx_sfeventmgt_domain_model_event`) in `packages/t3kit-sitepackage/Configuration/TCA/Overrides/tx_sfeventmgt_domain_model_event.php`, following `conn/t3kit-events`'s own `Configuration/TCA/Overrides/` as reference once installed. After any TCA/`ext_tables.sql` change: **Admin Tools → Maintenance → Analyze Database Structure**.

## 4. SCSS

Style overrides go in `packages/t3kit-sitepackage/Resources/Private/Scss/_theme.scss`, using the same `var(--conn-*)` design tokens as the rest of the project, then rebuild:

```bash
ddev exec "cd /var/www/html/packages/t3kit-sitepackage && npm run build"
```

## 5. PHP (Data Processors / Controllers)

Extend or replace behaviour by pointing at your own class under `packages/t3kit-sitepackage/Classes/` (PHP namespace `Connetation\T3kitSitepackage\`) rather than modifying `conn/t3kit-events` or `derhansen/sf_event_mgt` directly — same pattern as [extend-elements.md](extend-elements.md#5-php-data-processors-viewhelpers).
