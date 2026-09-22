# Creating a New Content Element

Scaffolds a new content element in `packages/t3kit-sitepackage`'s `conn/t3kit-elements` dependency, generating every required file and patching all registration files automatically.

## Prerequisites

- `ddev setup-project` has been run (project is installed, `ddev start` is running)
- `conn/t3kit-elements` is required in `composer.json` and listed under `dependencies` in `config/sites/sitepackage/config.yaml`
- Node.js available inside the DDEV web container (default — no local Node.js needed)

## What to run

```bash
ddev make-element
```

Or pass the element name directly to skip the first prompt:

```bash
ddev make-element "Hero Banner"
```

This is a custom host command (`.ddev/commands/host/make-element`) that opens an interactive terminal inside the web container so readline prompts work correctly.

It asks three questions:

1. **Element name** — e.g. `Hero Banner`, `Testimonial`, `Pricing Table`
2. **Type** — Simple (FlexForm settings only) or With inline child records (like Accordion / Cards)
3. **CE group** — `conn_bootstrap`, `conn`, `conn_swiper`, `conn_grid` (grid layout elements), or `default` (anything else falls back to `conn_bootstrap`)

   `default` is TYPO3 core's own group (`tt_content` `CType` `itemGroups.default`, labeled "Typical page content") — the same group `header` and `text` use — where core elements like Text & Media live; already used here for `conn_text_column`/`conn_text_icon`.

## What gets generated

| File | Purpose |
|------|---------|
| `Configuration/FlexForms/<Name>.xml` | Display settings form |
| `Configuration/TypoScript/ContentElements/<Name>.typoscript` | Frontend rendering setup |
| `Configuration/TCA/tx_conn_t3kit_elements_domain_model_<name>_item.php` | Child record TCA (with-items only) |
| `Classes/DataProcessing/<Name>ItemsProcessor.php` | DB query + FlexForm resolver (with-items only) |
| `Resources/Private/Templates/ContentElements/<Name>.html` | Main Fluid template |
| `Resources/Private/Partials/<Name>/Item.html` | Item partial (with-items only) |
| `Resources/Private/Components/<Name>/index.scss` | SCSS entry point |
| `Resources/Private/Components/<Name>/_<name>.scss` | SCSS component |
| `Resources/Private/Components/<Name>/<name>.ts` | TypeScript component |
| `Resources/Public/Icons/<name>-ce.svg` | Icon placeholder (replace with real SVG) |

## What gets patched automatically

`Configuration/Icons.php`, `build.mjs`, `ext_tables.sql`, `ext_localconf.php`,
`Configuration/TCA/Overrides/tt_content.php`, `Resources/Private/Language/locallang_be.xlf`,
`Configuration/TypoScript/setup.typoscript`

All patches are idempotent — re-running the script on an existing element name is safe.

## After running the scaffold

These steps must be done **in order** — skipping or reordering them causes an "Unknown column" SQL error when saving the element in the backend.

**1. Build assets**

```bash
ddev exec "cd /var/www/html/vendor/conn/t3kit-elements && npm run build"
```

**2. Flush TYPO3 caches** — so TYPO3 picks up the new TCA and SQL definitions

Admin Tools → Maintenance → Flush all caches

**3. Apply database schema** *(with-items elements only)*

Admin Tools → Maintenance → Analyze Database Structure → apply changes

Skipping this step leaves the new column missing from the database, which causes `SQL error: Unknown column '...' in 'INSERT INTO'` when saving the element.

**4. Verify** — the element should now appear in the "New Content Element" wizard under the chosen group.

## Customising the generated element

- **FlexForm** — edit `Configuration/FlexForms/<Name>.xml` to add/change display settings
- **Child record fields** — extend `Configuration/TCA/tx_conn_t3kit_elements_domain_model_<name>_item.php` and `ext_tables.sql`, then re-run DB analysis
- **Data Processor** — extend `resolveSettings()` and `$rows` enrichment in the generated processor
- **Template** — edit the Fluid template and partial freely
- **SCSS** — edit `_<name>.scss`; uses `var(--conn-*)` design tokens for consistent theming
- **TypeScript** — edit `<name>.ts`; the `registry.register()` call at the bottom wires it to the DOM
- **Icon** — replace `Resources/Public/Icons/<name>-ce.svg` with your SVG (16×16 viewBox recommended)

To override an already-generated element from `packages/t3kit-sitepackage` instead of editing `conn/t3kit-elements` directly, see [extend-elements.md](extend-elements.md).
