.. include:: /Includes.rst.txt

.. _pull-from-remote:

=================
Pull from remote
=================

Configure :file:`.ddev/commands/host/pull-db.yaml` with your server
details, then:

.. code-block:: bash

   ddev pull-db master
   ddev import-db --file=db_dump/master_YYYYMMDD_HHMM.sql.gz

   ddev pull-config master

.. caution::

   Neither ``pull-db`` nor ``pull-config`` currently asks for
   confirmation before connecting and pulling — double-check the
   environment name you pass matches what you intend, especially if a
   ``production`` environment is configured in
   :file:`pull-db.yaml` alongside staging ones.

``pull-db`` supports two environment types in :file:`pull-db.yaml`:

- **Classic** (default, no ``type:`` needed) — SSHes in, reads DB
  credentials from the server's
  :file:`config/system/settings.php`/:file:`additional.php`, and runs
  ``mysqldump`` there.
- **Docker** (``type: docker``) — for servers where the app runs via
  Docker Compose and the DB has no port published on the host. SSHes in
  and runs ``mariadb-dump`` *inside* the ``db`` container via
  ``docker compose exec``, using the container's own
  ``MARIADB_USER``/``MARIADB_PASSWORD`` env vars instead of a local
  TYPO3 config. Configure per environment:

  .. code-block:: yaml

     environments:
       production:
         type: docker
         host: your-server.example.com
         user: docker-user
         port: 22
         path: /opt/project           # directory containing the compose file
         compose_file: compose.prod.yaml   # optional, defaults to compose.prod.yaml
         service: db                       # optional, defaults to db
