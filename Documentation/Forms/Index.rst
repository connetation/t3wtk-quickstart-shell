.. include:: /Includes.rst.txt

.. _forms:

=====================
Contact Forms (EXT:form)
=====================

This template ships one example
`EXT:form <https://docs.typo3.org/c/typo3/cms-form/main/en-us/>`__ form
(:file:`Contact.form.yaml`) already wired up with client-side validation,
localization, and a branded HTML email — the plumbing to add further
forms is already in place, so a new form is mostly YAML + translation
strings, no code.

.. _forms-where:

Where things live
====================

- **Generic validation engine** — ``vendor/conn/base``'s
  :file:`Resources/Private/JavaScript/main.js` (``initFormValidation``).
  Fully data-attribute driven (``[required]`` / ``[data-validate-type]`` +
  matching ``data-validate-*-message``), so it needs zero changes for a
  new form or field. Don't edit it per-project — see
  :ref:`extending-elements` for why (same reasoning applies to
  ``conn/base``).
- **The actual form(s), email templates, and translations** —
  :file:`packages/sitepackage/Resources/Private/Forms/`,
  :file:`Templates/Email/`, :file:`Layouts/Email/`, :file:`Language/`.
  Fully owned by this project, customize freely (same as everything else
  under :file:`packages/sitepackage/`, see :ref:`customisation-sitepackage`).

.. _forms-full-guide:

Full guide
============

The detailed how-to — adding a new form, wiring up validation, adding a
language, adding an email template, and a note on why every path here is
a hardcoded ``EXT:conn_sitepackage/...`` reference — lives right next to
the code:
:file:`packages/sitepackage/Resources/Private/Forms/README.md` (only
present after ``ddev setup-project`` has copied ``conn/sitepackage`` into
:file:`packages/` — see :ref:`installation`).

.. _forms-quick-reference:

Quick reference
==================

- **New form** — duplicate :file:`Contact.form.yaml`, edit it in the
  backend Form module (already registered via the "Sitepackage" Form
  Set, :file:`Configuration/Form/Sitepackage/config.yaml`).
- **New field validation** — add ``required`` and/or
  ``data-validate-type`` plus the matching ``data-validate-*-message``
  under ``fluidAdditionalAttributes`` in the form YAML. No JS changes
  needed.
- **New language** — add the keys (English, source) to
  :file:`Resources/Private/Language/locallang_forms.xlf`, add the same
  key ``id``\ s with ``<target>`` translations to
  :file:`<iso>.locallang_forms.xlf`, and make sure the site
  (:file:`config/sites/sitepackage/config.yaml`) has that language under
  ``languages:``.
- **New email template** — set ``templateName: X`` on a finisher's
  ``options``, then add :file:`Templates/Email/X.fluid.html` /
  :file:`.fluid.txt`. Both extend the shared
  :file:`Layouts/Email/SystemEmail.fluid.html` branding chrome (site
  name, neutral header color) — pass
  ``variables: {logoUrl: ..., brandColor: ...}`` on a finisher to
  override those without touching the layout.
