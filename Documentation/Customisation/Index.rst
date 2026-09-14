.. include:: /Includes.rst.txt

.. _customisation:

=============
Customisation
=============

.. _customisation-sitepackage:

Sitepackage
===========

The sitepackage at :file:`packages/sitepackage/` is a full copy of
``conn/sitepackage``, freely editable:

- **CSS theme overrides** — edit
  :file:`Resources/Private/Scss/_theme.scss`, then:

  .. code-block:: bash

     ddev exec "cd /var/www/html/packages/sitepackage && npm run build"

  Or watch during development:

  .. code-block:: bash

     ddev exec "cd /var/www/html/packages/sitepackage && npm run dev"

- **Fluid template overrides** — place templates in
  :file:`Resources/Private/Templates/Page/`, :file:`Partials/`, or
  :file:`Layouts/` to override ``conn_base`` equivalents.

- **TypoScript** — edit :file:`Configuration/Sets/Base/setup.typoscript`.

- **TCA overrides** — :file:`Configuration/TCA/Overrides/`.

.. _customisation-site-settings:

Site settings
=============

Edit :file:`config/sites/sitepackage/settings.yaml` to configure navbar
style, footer layout, social links, branding, colours, etc.

All available settings with descriptions are documented in:
:file:`vendor/conn/base/Configuration/Sets/Base/settings.definitions.yaml`
