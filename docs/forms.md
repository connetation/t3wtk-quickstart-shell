# Contact Forms (EXT:form)

This template ships one example [EXT:form](https://docs.typo3.org/c/typo3/cms-form/main/en-us/) form (`Contact.form.yaml`) already wired up with client-side validation, localization, and a branded HTML email — the plumbing to add further forms is already in place, so a new form is mostly YAML + translation strings, no code.

## Where things live

- **Generic validation engine** — `vendor/conn/t3kit-base`'s `Resources/Private/JavaScript/main.js` (`initFormValidation`). Fully data-attribute driven (`[required]` / `[data-validate-type]` + matching `data-validate-*-message`), so it needs zero changes for a new form or field. Don't edit it per-project — see [Overwrite / extend conn/t3kit-elements](extend-elements.md) for why (same reasoning applies to `conn/t3kit-base`).
- **The actual form(s), email templates, and translations** — `packages/t3kit-sitepackage/Resources/Private/Forms/`, `Templates/Email/`, `Layouts/Email/`, `Language/`. Fully owned by this project, customize freely (same as everything else under `packages/t3kit-sitepackage/`, see the main [README](../README.md#sitepackage)).

## Full guide

The detailed how-to — adding a new form, wiring up validation, adding a
language, adding an email template, and a note on why every path here is a
hardcoded `EXT:conn_t3kit_sitepackage/...` reference — lives right next to the
code:
[`packages/t3kit-sitepackage/Resources/Private/Forms/README.md`](../packages/t3kit-sitepackage/Resources/Private/Forms/README.md)
(only present after `ddev setup-project` has copied `conn/t3kit-sitepackage` into
`packages/` — see [Starting a new project](../README.md#starting-a-new-project)).

## Quick reference

- **New form** — duplicate `Contact.form.yaml`, edit it in the backend Form
  module (already registered via the "Sitepackage" Form Set,
  `Configuration/Form/Sitepackage/config.yaml`).
- **New field validation** — add `required` and/or `data-validate-type` plus
  the matching `data-validate-*-message` under `fluidAdditionalAttributes` in
  the form YAML. No JS changes needed.
- **New language** — add the keys (English, source) to
  `Resources/Private/Language/locallang_forms.xlf`, add the same key `id`s
  with `<target>` translations to `<iso>.locallang_forms.xlf`, and make sure
  the site (`config/sites/sitepackage/config.yaml`) has that language under
  `languages:`.
- **New email template** — set `templateName: X` on a finisher's `options`,
  then add `Templates/Email/X.fluid.html` / `.fluid.txt`. Both extend the
  shared `Layouts/Email/SystemEmail.fluid.html` branding chrome (site name,
  neutral header color) — pass `variables: {logoUrl: ..., brandColor: ...}`
  on a finisher to override those without touching the layout.
