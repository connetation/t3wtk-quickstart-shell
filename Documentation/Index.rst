.. include:: /Includes.rst.txt

.. _start:

=========================
TYPO3 14 Project Template
=========================

:Package name:
   your-vendor/your-project *(rename after cloning — see* :ref:`installation` *)*

:Version:
   |release|

:Language:
   en

:License:
   This document is published under the
   `GPL-2.0-or-later <https://www.gnu.org/licenses/old-licenses/gpl-2.0.html>`__
   license.

:Rendered:
   |today|

----

.. note::

   This is a **project template**, not a TYPO3 extension — its
   ``composer.json`` declares ``"type": "project"``. It is not eligible
   for the TYPO3 Extension Repository (TER), which only accepts
   extensions. It is instead meant to be published on Packagist (or a
   public git host) and consumed with
   ``composer create-project <vendor>/<project> my-project``, the same
   distribution model as ``typo3/cms-base-distribution``.

A TYPO3 14 LTS project template built on
`Connetation <https://connetation.at>`__ base packages (``conn/t3kit-base``,
``conn/t3kit-elements``, ``conn/t3kit-sitepackage``). Run ``ddev setup-project`` and
get a working, fully-wired TYPO3 v14 instance in minutes.

.. _stack:

Stack
=====

- TYPO3 14 LTS
- PHP 8.3+
- MariaDB 10.11+
- Bootstrap 5 (via ``conn/t3kit-base``)
- Light/dark theme toggle
- DDEV for local development
- Deployer + GitLab CI/CD for deployment

----

**Table of Contents:**

.. toctree::
   :maxdepth: 2

   Introduction/Index
   Installation/Index
   Customisation/Index
   ContentElements/Index
   ExtendingElements/Index
   ExtendingEvents/Index
   ExtendingNews/Index
   Forms/Index
   Deployment/Index
   PullFromRemote/Index
