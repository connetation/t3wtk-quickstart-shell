.. include:: /Includes.rst.txt

.. _content-elements:

================
Content Elements
================

Scaffolds a new content element in :file:`packages/t3kit-sitepackage`'s
``conn/t3kit-elements`` dependency, generating every required file and patching
all registration files automatically.

.. _content-elements-prerequisites:

Prerequisites
==============

- ``ddev setup-project`` has been run (project is installed, ``ddev
  start`` is running)
- ``conn/t3kit-elements`` is required in :file:`composer.json` and listed under
  ``dependencies`` in :file:`config/sites/sitepackage/config.yaml`
- Node.js available inside the DDEV web container (default — no local
  Node.js needed)

.. _content-elements-run:

What to run
=============

.. code-block:: bash

   ddev make-element

Or pass the element name directly to skip the first prompt:

.. code-block:: bash

   ddev make-element "Hero Banner"

This is a custom host command
(:file:`.ddev/commands/host/make-element`) that opens an interactive
terminal inside the web container so readline prompts work correctly.

It asks three questions:

#. **Element name** — e.g. ``Hero Banner``, ``Testimonial``, ``Pricing Table``
#. **Type** — Simple (FlexForm settings only) or With inline child records
   (like Accordion / Cards)
#. **CE group** — ``conn_bootstrap``, ``conn``, ``conn_swiper``,
   ``conn_grid`` (grid layout elements), or ``default`` (anything else
   falls back to ``conn_bootstrap``)

   ``default`` is TYPO3 core's own group (``tt_content`` ``CType``
   ``itemGroups.default``, labeled "Typical page content") — the same
   group ``header`` and ``text`` use — where core elements like Text &
   Media live; already used here for ``conn_text_column``/``conn_text_icon``.

.. _content-elements-generated:

What gets generated
=====================

.. list-table::
   :header-rows: 1
   :widths: 60 40

   * - File
     - Purpose
   * - ``Configuration/FlexForms/<Name>.xml``
     - Display settings form
   * - ``Configuration/TypoScript/ContentElements/<Name>.typoscript``
     - Frontend rendering setup
   * - ``Configuration/TCA/tx_conn_t3kit_elements_domain_model_<name>_item.php``
     - Child record TCA (with-items only)
   * - ``Classes/DataProcessing/<Name>ItemsProcessor.php``
     - DB query + FlexForm resolver (with-items only)
   * - ``Resources/Private/Templates/ContentElements/<Name>.html``
     - Main Fluid template
   * - ``Resources/Private/Partials/<Name>/Item.html``
     - Item partial (with-items only)
   * - ``Resources/Private/Components/<Name>/index.scss``
     - SCSS entry point
   * - ``Resources/Private/Components/<Name>/_<name>.scss``
     - SCSS component
   * - ``Resources/Private/Components/<Name>/<name>.ts``
     - TypeScript component
   * - ``Resources/Public/Icons/<name>-ce.svg``
     - Icon placeholder (replace with real SVG)

.. _content-elements-patched:

What gets patched automatically
==================================

:file:`Configuration/Icons.php`, :file:`build.mjs`, :file:`ext_tables.sql`,
:file:`ext_localconf.php`, :file:`Configuration/TCA/Overrides/tt_content.php`,
:file:`Resources/Private/Language/locallang_be.xlf`,
:file:`Configuration/TypoScript/setup.typoscript`.

All patches are idempotent — re-running the script on an existing element
name is safe.

.. _content-elements-after:

After running the scaffold
=============================

These steps must be done **in order** — skipping or reordering them
causes an "Unknown column" SQL error when saving the element in the
backend.

#. **Build assets**

   .. code-block:: bash

      ddev exec "cd /var/www/html/vendor/conn/t3kit-elements && npm run build"

#. **Flush TYPO3 caches** — so TYPO3 picks up the new TCA and SQL
   definitions. Admin Tools → Maintenance → Flush all caches.
#. **Apply database schema** *(with-items elements only)* — Admin Tools →
   Maintenance → Analyze Database Structure → apply changes. Skipping
   this step leaves the new column missing from the database, which
   causes a ``SQL error: Unknown column '...' in 'INSERT INTO'`` when
   saving the element.
#. **Verify** — the element should now appear in the "New Content
   Element" wizard under the chosen group.

.. _content-elements-customising:

Customising the generated element
=====================================

- **FlexForm** — edit :file:`Configuration/FlexForms/<Name>.xml` to
  add/change display settings.
- **Child record fields** — extend
  :file:`Configuration/TCA/tx_conn_t3kit_elements_domain_model_<name>_item.php`
  and :file:`ext_tables.sql`, then re-run DB analysis.
- **Data Processor** — extend ``resolveSettings()`` and ``$rows``
  enrichment in the generated processor.
- **Template** — edit the Fluid template and partial freely.
- **SCSS** — edit ``_<name>.scss``; uses ``var(--conn-*)`` design tokens
  for consistent theming.
- **TypeScript** — edit ``<name>.ts``; the ``registry.register()`` call
  at the bottom wires it to the DOM.
- **Icon** — replace :file:`Resources/Public/Icons/<name>-ce.svg` with
  your SVG (16×16 viewBox recommended).

To override an already-generated element from :file:`packages/t3kit-sitepackage`
instead of editing ``conn/t3kit-elements`` directly, see :ref:`extending-elements`.
