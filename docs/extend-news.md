# Overwriting / Extending `conn/news`

> **Not yet part of this template.** `conn/news` isn't in `composer.json` or `vendor/conn/` at the time of writing. This doc describes it based on how the equivalent Connetation package `conn/base` wraps its upstream dependency — the same shape applies here once `conn/news` is added. Update the specifics below once the package actually exists in `vendor/conn/news/`.

`conn/news` is a Connetation **Site Set** that requires and overrides [`georgringer/news`](https://packagist.org/packages/georgringer/news) (extension key `news`) — same relationship as `conn/base` has to `typo3/fluid-styled-content`: it pulls in the upstream extension as a Composer dependency, declares it as a Set dependency, and ships its own TypoScript/TCA/Fluid templates on top so News output matches the project's Bootstrap 5 theme out of the box.

Don't edit `vendor/conn/news/` or `vendor/georgringer/news/` directly — project-specific customisation happens in `packages/sitepackage/`, which is fully owned by this project (see the main [README](../README.md#sitepackage)).

## 1. Composer + Site Set dependency

```bash
composer require conn/news
```

`conn/news`'s own `composer.json` requires `georgringer/news`, so it comes along automatically. Add `conn/news` (not `georgringer/news`) to `config/sites/sitepackage/config.yaml` — its Set declares the dependency on the `georgringer/news` Set, cascading it in:

```yaml
dependencies:
  - conn/sitepackage
  - conn/elements
  - conn/news
```

## 2. Template overrides (Fluid)

`georgringer/news` resolves templates via array-based paths under `plugin.tx_news.view`:

```typoscript
plugin.tx_news {
    view {
        templateRootPaths.20 = EXT:conn_news/Resources/Private/Templates/
        partialRootPaths.20  = EXT:conn_news/Resources/Private/Partials/
        layoutRootPaths.20   = EXT:conn_news/Resources/Private/Layouts/
    }
}
```

`conn/news` already adds its own index (e.g. `20`) on top of `georgringer/news`'s defaults (`0`/`10`). To override a template from the sitepackage, add a higher index in `packages/sitepackage/Configuration/Sets/Sitepackage/setup.typoscript`, same pattern as the `lib.contentElement` override for `conn/elements` (see [extend-elements.md](extend-elements.md#2-template-overrides-fluid)):

```typoscript
plugin.tx_news.view {
    templateRootPaths.50 = EXT:conn_sitepackage/Resources/Private/Extensions/News/Templates/
    partialRootPaths.50  = EXT:conn_sitepackage/Resources/Private/Extensions/News/Partials/
    layoutRootPaths.50   = EXT:conn_sitepackage/Resources/Private/Extensions/News/Layouts/
}
```

Higher index wins, and only the specific file you place there is overridden — everything else still resolves from `conn/news`.

## 3. TCA overrides

Extend the news domain model (table `tx_news_domain_model_news`) in `packages/sitepackage/Configuration/TCA/Overrides/tx_news_domain_model_news.php`, following `conn/news`'s own `Configuration/TCA/Overrides/` as reference once installed. After any TCA/`ext_tables.sql` change: **Admin Tools → Maintenance → Analyze Database Structure**.

## 4. SCSS

Style overrides go in `packages/sitepackage/Resources/Private/Scss/_theme.scss`, using the same `var(--conn-*)` design tokens as the rest of the project, then rebuild:

```bash
ddev exec "cd /var/www/html/packages/sitepackage && npm run build"
```

## 5. PHP (Data Processors / Controllers)

Extend or replace behaviour by pointing at your own class under `packages/sitepackage/Classes/` (PHP namespace `Connetation\Sitepackage\`) rather than modifying `conn/news` or `georgringer/news` directly — same pattern as [extend-elements.md](extend-elements.md#5-php-data-processors-viewhelpers).
