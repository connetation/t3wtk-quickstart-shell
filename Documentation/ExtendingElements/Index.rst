.. include:: /Includes.rst.txt

.. _extending-elements:

===============================
Overwriting / Extending ``conn/elements``
===============================

``conn/elements`` is a Composer-installed extension
(:file:`vendor/conn/elements/`) — don't edit it directly, those changes
are lost on ``composer update``. All project-specific customisation
happens in :file:`packages/sitepackage/`, which is fully owned by this
project (see :ref:`customisation-sitepackage`).

.. _extending-elements-site-set:

1. Site Set dependency
=========================

``conn/elements`` must be listed as a dependency in
:file:`config/sites/sitepackage/config.yaml`:

.. code-block:: yaml

   dependencies:
     - conn/sitepackage
     - conn/elements

Without this, its TypoScript/TCA never loads for the site.

.. _extending-elements-templates:

2. Template overrides (Fluid)
================================

:file:`packages/sitepackage/Configuration/Sets/Sitepackage/setup.typoscript`
already declares override paths at index ``50``, which are checked before
``conn_elements`` (``40``) and ``conn_base`` (``30``):

.. code-block:: typoscript

   lib.contentElement {
       templateRootPaths.50 = EXT:conn_sitepackage/Resources/Private/Templates/
       partialRootPaths.50  = EXT:conn_sitepackage/Resources/Private/Partials/
       layoutRootPaths.50   = EXT:conn_sitepackage/Resources/Private/Layouts/
   }

To override a specific element's template, drop a same-named file at the
matching path in
:file:`packages/sitepackage/Resources/Private/Templates/ContentElements/<Name>.html`
(and :file:`Partials/<Name>/`, :file:`Layouts/` as needed) — Fluid
resolves the higher-index path first.

.. important::

   **Existing CTypes need an explicit path too.** ``fluid_styled_content``
   and ``conn_elements`` copy ``lib.contentElement`` into each
   ``tt_content.<CType>`` with ``=<`` *before* the sitepackage Set loads,
   so the generic path above only reaches content elements registered
   *after* it. To override an existing element's template, set the path
   directly on its CType:

   .. code-block:: typoscript

      tt_content.textmedia.templateRootPaths.50 = EXT:conn_sitepackage/Resources/Private/Templates/

.. _extending-elements-tca:

3. TCA overrides
===================

Add/change fields on an existing element (or its child records) in
:file:`packages/sitepackage/Configuration/TCA/Overrides/tt_content.php`.
Follow the existing pattern in
:file:`vendor/conn/elements/Configuration/TCA/Overrides/tt_content.php`
for reference — copy the structure, don't edit the vendor file.

After changing TCA or :file:`ext_tables.sql`: **Admin Tools →
Maintenance → Analyze Database Structure** to apply schema changes.

.. _extending-elements-scss:

4. SCSS
=========

Element styles use ``var(--conn-*)`` design tokens defined in
``conn/base``. Override tokens or add component-specific overrides in
:file:`packages/sitepackage/Resources/Private/Scss/_theme.scss`, then
rebuild:

.. code-block:: bash

   ddev exec "cd /var/www/html/packages/sitepackage && npm run build"

.. _extending-elements-php:

5. PHP (Data Processors, ViewHelpers)
========================================

Extend or replace a ``conn/elements`` Data Processor by pointing the
element's TypoScript at your own class under
:file:`packages/sitepackage/Classes/DataProcessing/` (PHP namespace
``Connetation\Sitepackage\``), rather than modifying the vendor class.

.. _extending-elements-new:

Adding a brand-new element
=============================

Use the scaffold CLI instead of hand-writing all of the above — see
:ref:`content-elements`.
