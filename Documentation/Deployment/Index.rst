.. include:: /Includes.rst.txt

.. _deployment:

==========
Deployment
==========

.. _deployment-credentials:

1. Configure server credentials
==================================

Edit :file:`deployer/hosts.yaml`:

.. code-block:: yaml

   hosts:
     main:
       hostname: your-server.example.com
       user: your-deploy-user
       port: 22
       deploy_path: /path/to/deploy
       writable_mode: chmod
       forward_agent: true
       keep_releases: 2
       socket: /var/run/php/php8.3-fpm.sock

Edit :file:`.gitlab-ci.yml` variables:

.. code-block:: yaml

   PRODUCTION_HOST: "your-server.example.com"
   PRODUCTION_USER: "your-deploy-user"

Add ``SSH_PRIVATE_KEY_PRODUCTION`` to GitLab CI/CD variables (project →
Settings → CI/CD → Variables → Add variable):

- **Type**: ``File`` (not ``Variable``) — the pipeline reads the key
  from the path GitLab injects (``ssh-add "$SSH_PRIVATE_KEY_PRODUCTION"``),
  not from the variable's value directly. ``File`` type also keeps the
  key out of the job's environment variable list, and it's required here
  anyway since a multi-line SSH key can't use GitLab's ``Masked`` option.
  Note: GitLab writes ``File`` type variables to disk as ``0666``, so the
  pipeline runs ``chmod 600`` on it before ``ssh-add`` — otherwise
  ``ssh-add`` refuses the key with "UNPROTECTED PRIVATE KEY FILE".
- **Protected**: on, so the key is only exposed to pipelines running on
  protected branches (e.g. ``main``) — make sure ``main`` is a protected
  branch before relying on this.
- Paste the private key exactly as generated (e.g. ``~/.ssh/id_ed25519``),
  including the ``-----BEGIN...-----``/``-----END...-----`` lines.

.. _deployment-additional-php:

2. Production ``config/system/additional.php``
==================================================

On the server, create this deployer shared file:

.. code-block:: php

   <?php
   $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'] = [
       'driver'   => 'mysqli',
       'host'     => 'localhost',
       'port'     => 3306,
       'dbname'   => 'your_db',
       'user'     => 'your_user',
       'password' => 'your_password',
       'charset'  => 'utf8mb4',
   ];
   $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'your-64-char-key';

.. _deployment-site-config-local:

3. Production ``config/sites/sitepackage/config.local.yaml``
=================================================================

Also a deployer shared file — override the base URL for production:

.. code-block:: yaml

   base: 'https://your-domain.com/'
