.. include:: /Includes.rst.txt

.. _extending-news:

===========================
Overwriting / Extending ``conn/news``
===========================

.. attention::

   **Not yet part of this template.** ``conn/news`` isn't in
   :file:`composer.json` or :file:`vendor/conn/` at the time of writing.
   This page describes it based on how the equivalent Connetation package
   ``conn/base`` wraps its upstream dependency — the same shape applies
   here once ``conn/news`` is added. Update the specifics below once the
   package actually exists in :file:`vendor/conn/news/`.

``conn/news`` is a Connetation **Site Set** that requires and overrides
`georgringer/news <https://packagist.org/packages/georgringer/news>`__
(extension key ``news``) — same relationship as ``conn/base`` has to
``typo3/fluid-styled-content``: it pulls in the upstream extension as a
Composer dependency, declares it as a Set dependency, and ships its own
TypoScript/TCA/Fluid templates on top so News output matches the
project's Bootstrap 5 theme out of the box.

Don't edit :file:`vendor/conn/news/` or :file:`vendor/georgringer/news/`
directly — project-specific customisation happens in
:file:`packages/sitepackage/`, which is fully owned by this project (see
:ref:`customisation-sitepackage`).

.. _extending-news-site-set:

1. Composer + Site Set dependency
=====================================

.. code-block:: bash

   composer require conn/news

``conn/news``'s own :file:`composer.json` requires ``georgringer/news``,
so it comes along automatically. Add ``conn/news`` (not
``georgringer/news``) to :file:`config/sites/sitepackage/config.yaml` —
its Set declares the dependency on the ``georgringer/news`` Set,
cascading it in:

.. code-block:: yaml

   dependencies:
     - conn/sitepackage
     - conn/elements
     - conn/news

.. _extending-news-templates:

2. Template overrides (Fluid)
================================

``georgringer/news`` resolves templates via array-based paths under
``plugin.tx_news.view``:

.. code-block:: typoscript

   plugin.tx_news {
       view {
           templateRootPaths.20 = EXT:conn_news/Resources/Private/Templates/
           partialRootPaths.20  = EXT:conn_news/Resources/Private/Partials/
           layoutRootPaths.20   = EXT:conn_news/Resources/Private/Layouts/
       }
   }

``conn/news`` already adds its own index (e.g. ``20``) on top of
``georgringer/news``'s defaults (``0``/``10``). To override a template
from the sitepackage, add a higher index in
:file:`packages/sitepackage/Configuration/Sets/Sitepackage/setup.typoscript`,
same pattern as the ``lib.contentElement`` override for ``conn/elements``
(see :ref:`extending-elements-templates`):

.. code-block:: typoscript

   plugin.tx_news.view {
       templateRootPaths.50 = EXT:conn_sitepackage/Resources/Private/Extensions/News/Templates/
       partialRootPaths.50  = EXT:conn_sitepackage/Resources/Private/Extensions/News/Partials/
       layoutRootPaths.50   = EXT:conn_sitepackage/Resources/Private/Extensions/News/Layouts/
   }

Higher index wins, and only the specific file you place there is
overridden — everything else still resolves from ``conn/news``.

.. _extending-news-tca:

3. TCA overrides
===================

Extend the news domain model (table ``tx_news_domain_model_news``) in
:file:`packages/sitepackage/Configuration/TCA/Overrides/tx_news_domain_model_news.php`,
following ``conn/news``'s own :file:`Configuration/TCA/Overrides/` as
reference once installed. After any TCA/:file:`ext_tables.sql` change:
**Admin Tools → Maintenance → Analyze Database Structure**.

.. _extending-news-scss:

4. SCSS
=========

Style overrides go in
:file:`packages/sitepackage/Resources/Private/Scss/_theme.scss`, using
the same ``var(--conn-*)`` design tokens as the rest of the project, then
rebuild:

.. code-block:: bash

   ddev exec "cd /var/www/html/packages/sitepackage && npm run build"

.. _extending-news-php:

5. PHP (Data Processors / Controllers)
=========================================

Extend or replace behaviour by pointing at your own class under
:file:`packages/sitepackage/Classes/` (PHP namespace
``Connetation\Sitepackage\``) rather than modifying ``conn/news`` or
``georgringer/news`` directly — same pattern as
:ref:`extending-elements-php`.
