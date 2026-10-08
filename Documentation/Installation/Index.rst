.. include:: /Includes.rst.txt

.. _installation:

=====================
Starting a new project
=====================

.. _installation-clone:

1. Clone
========

.. code-block:: bash

   git clone <this-repo> my-project.com
   cd my-project.com

There's no need to manually edit :file:`.ddev/config.yaml` or
:file:`composer.json` — ``ddev setup-project`` (step 3) prompts for the
project name/subdomain and renames things for you.

.. warning::

   **Watch out for name collisions:** this repo's :file:`.ddev/config.yaml`
   ships with a fixed default project name. If you've already used this
   template before and still have that other project registered under the
   same name, DDEV will error when it tries to start/rename this checkout.
   Run ``ddev list`` to check for a clash, then ``ddev stop <old-name>``
   (or ``ddev delete <old-name>`` to remove it entirely) before continuing.

.. _installation-auth:

2. Add Composer authentication — required
============================================

``connetation/t3wtk-base``, ``connetation/t3wtk-elements`` and ``connetation/t3wtk-sitepackage`` are installed
from the private Connetation Composer repository
(``https://composer-v2.connetation.at/``), which requires authentication.
Create :file:`auth.json` in the project root (it's gitignored, so it's
never committed):

.. code-block:: json

   {
       "http-basic": {
           "composer-v2.connetation.at": {
               "username": "your-username",
               "password": "your-password"
           }
       }
   }

Contact Connetation if you don't have access credentials.
``ddev setup-project`` checks for this file and refuses to run without it.

.. _installation-setup:

3. Run setup (once)
======================

.. code-block:: bash

   ddev setup-project

No need to run ``ddev start`` first — ``ddev setup-project`` starts the
project automatically if it isn't running yet.

The command prompts for:

.. list-table::
   :header-rows: 1
   :widths: 20 25 30

   * - Prompt
     - Default
     - Saved to
   * - Project name
     - Derived from DDEV name
     - ``config/sites/…/config.yaml`` websiteTitle + TYPO3 site name
   * - DDEV subdomain
     - Current ``name:`` in :file:`.ddev/config.yaml`
     - :file:`.ddev/config.yaml`, DDEV restart if changed
   * - Admin username
     - ``admin``
     - TYPO3 admin account
   * - Admin password
     - ``Admin1234!``
     - TYPO3 admin account
   * - Admin email
     - ``admin@example.com``
     - TYPO3 admin account
   * - Languages
     - ``Deutsch`` (menu from :file:`languages.conf`)
     - ``config/sites/…/config.yaml`` ``languages:`` block

Pick one or more languages, comma-separated (e.g. ``1,2,4``), or ``Other``
to enter a custom one (title, ISO 639-1 code, locale, flag, hreflang) on
the spot. The **first** language picked becomes the default
(``languageId: 0``, served at ``/``); every other one gets its own
``languageId``, is served at ``/<code>/``, and falls back to the default
language for untranslated pages. Edit
:file:`.ddev/commands/host/languages.conf` to change the menu itself.

Then does the following in order:

.. list-table::
   :header-rows: 1
   :widths: 10 90

   * - Step
     - What happens
   * - 1
     - ``composer install`` — installs all dependencies including ``connetation/t3wtk-sitepackage`` from VCS
   * - 2
     - Creates :file:`public/fileadmin/`, :file:`var/`, :file:`translations/` directories
   * - 3
     - Copies :file:`vendor/connetation/t3wtk-sitepackage/` → :file:`packages/t3wtk-sitepackage/`, removes ``.git``
   * - 4
     - Updates :file:`composer.json`: removes VCS sitepackage repo, sets ``connetation/t3wtk-sitepackage: dev-local``
   * - 5
     - ``composer update connetation/t3wtk-sitepackage`` — rewires vendor symlink to :file:`packages/t3wtk-sitepackage/`
   * - 6
     - ``typo3 setup`` — creates DB schema and admin user, then ``extension:setup`` + ``cache:flush``
   * - 7
     - Builds sitepackage CSS (``npm install && npm run build``) — skipped if installed from dist archive
   * - 8
     - Removes the template's ``.git`` history and runs ``git init`` — your project starts from a clean slate

.. _installation-first-commit:

4. Create your initial commit
================================

``setup-project`` wipes the template's ``.git`` history and reinitializes
an empty repository, so nothing is committed yet. Make your first commit
for the project:

.. code-block:: bash

   git add -A
   git commit -m "Initial commit"

From this point on, :file:`packages/t3wtk-sitepackage/` is fully owned by this
project. All customisations are committed here — no connection to the
upstream ``connetation/t3wtk-sitepackage`` VCS remains.

.. _installation-first-login:

5. First login
=================

Open ``https://<project-name>.ddev.site/typo3``

- Username: ``admin``
- Password: ``Admin1234!`` — **change this immediately**

On a successful run, ``setup-project`` has already created and wired up
two pages for you — there is no manual root-page setup step:

- **uid 1** — the root page, titled with the project name you entered
  during setup, marked as site root (``is_siteroot``)
- **uid 2** — "Seite nicht gefunden" ("Page not found"), active but
  hidden from menus, wired up as the 404 page via
  :file:`config/sites/sitepackage/config.yaml`'s ``errorHandling``

Log into the TYPO3 backend and both pages are already there, ready to
fill with content.
