.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

.. _project-structure:

Project structure
==================

::

   .
   ├── auth.json                         Composer credentials for the private Connetation repo
   │                                     (gitignored — create manually, see Installation)
   ├── .ddev/
   │   ├── config.yaml                   DDEV settings (name: set interactively by setup-project)
   │   └── commands/host/
   │       ├── setup-project             Fresh TYPO3 install (run once after clone)
   │       ├── languages.conf            Language menu offered by setup-project (edit to add options)
   │       ├── make-element              Scaffold new content elements
   │       ├── pull-db                   Download DB dump from remote
   │       ├── pull-config               Sync site config from remote
   │       └── pull-db.yaml              Remote server config (fill in after deploy)
   ├── config/
   │   ├── sites/sitepackage/
   │   │   ├── config.yaml               Site config (URL, languages, set dependencies)
   │   │   └── settings.yaml             Theme, footer, branding overrides
   │   └── system/                       DB credentials (gitignored, auto-generated)
   ├── deployer/
   │   ├── deploy.php                    Deployer rsync tasks and shared-file config
   │   └── hosts.yaml                    Server credentials (hostname, user, path, socket)
   ├── packages/                         Created by ddev setup-project
   │   └── t3kit-sitepackage/            conn/t3kit-sitepackage copied here, .git removed
   │                                     Tracked by this project's git — customize freely
   ├── public/                           TYPO3 web root
   │   ├── index.php
   │   └── .htaccess
   ├── translations/                     Translation override files
   ├── .gitlab-ci.yml                    CI/CD pipeline (fill in server variables)
   └── composer.json

.. _extension-hierarchy:

How the pieces fit together
=============================

::

   conn-project-template (this repo, becomes your project after setup)
   ├── packages/t3kit-sitepackage/  (a full, git-detached copy of conn/t3kit-sitepackage — yours to edit)
   │   └── depends on: conn/t3kit-base
   ├── conn/t3kit-base        (Composer package — reusable foundation, never edit directly)
   └── conn/t3kit-elements    (Composer package, optional — content elements, never edit directly)

The key idea: ``conn/t3kit-base`` and ``conn/t3kit-elements`` stay Composer-managed
vendor packages you never touch directly (edits would be lost on
``composer update``). ``conn/t3kit-sitepackage`` is different — ``setup-project``
copies it into :file:`packages/t3kit-sitepackage/`, detaches it from its
upstream git history, and rewires Composer to load it from that local
path. From that point on it belongs entirely to your project.
