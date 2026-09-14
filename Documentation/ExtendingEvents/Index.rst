.. include:: /Includes.rst.txt

.. _extending-events:

=============================
Overwriting / Extending ``conn/events``
=============================

.. attention::

   **Not yet part of this template.** ``conn/events`` isn't in
   :file:`composer.json` or :file:`vendor/conn/` at the time of writing.
   This page describes it based on how the equivalent Connetation package
   ``conn/base`` wraps its upstream dependency — the same shape applies
   here once ``conn/events`` is added. Update the specifics below once
   the package actually exists in :file:`vendor/conn/events/`.

``conn/events`` is a Connetation **Site Set** that requires and overrides
`derhansen/sf_event_mgt <https://packagist.org/packages/derhansen/sf_event_mgt>`__
(extension key ``sf_event_mgt``) — same relationship as ``conn/base`` has
to ``typo3/fluid-styled-content``: it pulls in the upstream extension as
a Composer dependency, declares it as a Set dependency, and ships its own
TypoScript/TCA/Fluid templates on top so event output matches the
project's Bootstrap 5 theme out of the box.

Don't edit :file:`vendor/conn/events/` or
:file:`vendor/derhansen/sf_event_mgt/` directly — project-specific
customisation happens in :file:`packages/sitepackage/`, which is fully
owned by this project (see :ref:`customisation-sitepackage`).

.. _extending-events-site-set:

1. Composer + Site Set dependency
=====================================

.. code-block:: bash

   composer require conn/events

``conn/events``'s own :file:`composer.json` requires
``derhansen/sf_event_mgt``, so it comes along automatically. Add
``conn/events`` (not ``derhansen/sf_event_mgt``) to
:file:`config/sites/sitepackage/config.yaml` — its Set declares the
dependency on the ``sf_event_mgt`` Set, cascading it in:

.. code-block:: yaml

   dependencies:
     - conn/sitepackage
     - conn/elements
     - conn/events

.. _extending-events-templates:

2. Template overrides (Fluid)
================================

``sf_event_mgt`` resolves templates via array-based paths under
``plugin.tx_sfeventmgt.view``:

.. code-block:: typoscript

   plugin.tx_sfeventmgt {
       view {
           templateRootPaths.20 = EXT:conn_events/Resources/Private/Templates/
           partialRootPaths.20  = EXT:conn_events/Resources/Private/Partials/
           layoutRootPaths.20   = EXT:conn_events/Resources/Private/Layouts/
       }
   }

``conn/events`` already adds its own index (e.g. ``20``) on top of
``sf_event_mgt``'s defaults. To override a template from the sitepackage,
add a higher index in
:file:`packages/sitepackage/Configuration/Sets/Sitepackage/setup.typoscript`,
same pattern as the ``lib.contentElement`` override for ``conn/elements``
(see :ref:`extending-elements-templates`):

.. code-block:: typoscript

   plugin.tx_sfeventmgt.view {
       templateRootPaths.50 = EXT:conn_sitepackage/Resources/Private/Extensions/SfEventMgt/Templates/
       partialRootPaths.50  = EXT:conn_sitepackage/Resources/Private/Extensions/SfEventMgt/Partials/
       layoutRootPaths.50   = EXT:conn_sitepackage/Resources/Private/Extensions/SfEventMgt/Layouts/
   }

Higher index wins, and only the specific file you place there is
overridden — everything else still resolves from ``conn/events``.

.. note::

   Since TYPO3 12.4, ``sf_event_mgt``'s **backend** template overrides
   (event administration module) use TSConfig rather than TypoScript,
   e.g. ``templates.derhansen/sf_event_mgt.20 =
   EXT:conn_sitepackage/Resources/Private/...`` — only frontend plugin
   templates use the ``plugin.tx_sfeventmgt.view`` TypoScript shown above.

.. _extending-events-tca:

3. TCA overrides
===================

Extend the event domain model (table
``tx_sfeventmgt_domain_model_event``) in
:file:`packages/sitepackage/Configuration/TCA/Overrides/tx_sfeventmgt_domain_model_event.php`,
following ``conn/events``'s own :file:`Configuration/TCA/Overrides/` as
reference once installed. After any TCA/:file:`ext_tables.sql` change:
**Admin Tools → Maintenance → Analyze Database Structure**.

.. _extending-events-scss:

4. SCSS
=========

Style overrides go in
:file:`packages/sitepackage/Resources/Private/Scss/_theme.scss`, using
the same ``var(--conn-*)`` design tokens as the rest of the project, then
rebuild:

.. code-block:: bash

   ddev exec "cd /var/www/html/packages/sitepackage && npm run build"

.. _extending-events-php:

5. PHP (Data Processors / Controllers)
=========================================

Extend or replace behaviour by pointing at your own class under
:file:`packages/sitepackage/Classes/` (PHP namespace
``Connetation\Sitepackage\``) rather than modifying ``conn/events`` or
``derhansen/sf_event_mgt`` directly — same pattern as
:ref:`extending-elements-php`.
