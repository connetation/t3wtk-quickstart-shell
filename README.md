# TYPO3 14 Project Template

A TYPO3 14 LTS project template built on [Connetation](https://connetation.at) base packages (`connetation/t3wtk-base`, `connetation/t3wtk-elements`, `connetation/t3wtk-sitepackage`).

## Stack

- TYPO3 14 LTS
- PHP 8.3+
- MariaDB 10.11+
- Bootstrap 5 (via `connetation/t3wtk-base`)
- Light/dark theme toggle
- DDEV for local development
- Deployer + GitLab CI/CD for deployment

## Documentation

- [Create a new content element](docs/create-new-element.md) — running `ddev make-element`, what gets generated, required post-scaffold steps
- [Overwrite / extend connetation/t3wtk-elements](docs/extend-elements.md)
- [Overwrite / extend connetation/news](docs/extend-news.md)
- [Overwrite / extend connetation/events](docs/extend-events.md)
- [Contact forms (EXT:form)](docs/forms.md) — the bundled Contact form, adding new forms/fields, validation, localization, email templates

## Project structure

```
.
├── .ddev/
│   ├── config.yaml                   DDEV settings (name: set interactively by setup-project)
│   └── commands/host/
│       ├── setup-project             Fresh TYPO3 install (run once after clone)
│       ├── languages.conf            Language menu offered by setup-project (edit to add options)
│       ├── make-element              Scaffold new content elements
│       ├── pull-db                   Download DB dump from remote
│       ├── pull-config               Sync site config from remote
│       └── pull-db.yaml             Remote server config (fill in after deploy)
├── config/
│   ├── sites/sitepackage/
│   │   ├── config.yaml               Site config (URL, languages, set dependencies)
│   │   └── settings.yaml             Theme, footer, branding overrides
│   └── system/                       DB credentials (gitignored, auto-generated)
├── deployer/
│   ├── deploy.php                    Deployer rsync tasks and shared-file config
│   └── hosts.yaml                    Server credentials (hostname, user, path, socket)
├── packages/                         Created by ddev setup-project
│   └── t3wtk-sitepackage/            connetation/t3wtk-sitepackage copied here, .git removed
│                                     Tracked by this project's git — customize freely
├── public/                           TYPO3 web root
│   ├── index.php
│   └── .htaccess
├── translations/                     Translation override files
├── .gitlab-ci.yml                    CI/CD pipeline (fill in server variables)
└── composer.json
```

## Starting a new project

### 1. Clone

```bash
git clone https://github.com/connetation/t3wtk-quickstart-shell.git my-project.com
cd my-project.com
```

There's no need to manually edit `.ddev/config.yaml` or `composer.json` —
`ddev setup-project` (step 2) prompts for the project name/subdomain and
renames things for you.

> **Watch out for name collisions:** this repo's `.ddev/config.yaml` ships with
> a fixed default project name. If you've already used this template before
> and still have that other project registered under the same name, DDEV
> will error when it tries to start/rename this checkout. Run `ddev list` to
> check for a clash, then `ddev stop <old-name>` (or `ddev delete <old-name>`
> to remove it entirely) before continuing.

### 2. Run setup (once)

```bash
ddev setup-project
```

No need to run `ddev start` first — `ddev setup-project` starts the project
automatically if it isn't running yet.

The command prompts for:

| Prompt | Default | Saved to |
|--------|---------|----------|
| Project name | Derived from DDEV name | `config/sites/…/config.yaml` websiteTitle + TYPO3 site name |
| DDEV subdomain | Current `name:` in `.ddev/config.yaml` | `.ddev/config.yaml`, DDEV restart if changed |
| Admin username | `admin` | TYPO3 admin account |
| Admin password | `Admin1234!` | TYPO3 admin account |
| Admin email | `admin@example.com` | TYPO3 admin account |
| Languages | `Deutsch` (menu from `languages.conf`) | `config/sites/…/config.yaml` `languages:` block |

Pick one or more languages, comma-separated (e.g. `1,2,4`), or `Other` to enter
a custom one (title, ISO 639-1 code, locale, flag, hreflang) on the spot. The
**first** language picked becomes the default (`languageId: 0`, served at `/`);
every other one gets its own `languageId`, is served at `/<code>/`, and
falls back to the default language for untranslated pages. Edit
`.ddev/commands/host/languages.conf` to change the menu itself.

Then does the following in order:

| Step | What happens |
|------|-------------|
| 1 | `composer install` — installs all dependencies including `connetation/t3wtk-sitepackage` (as git source) from Packagist |
| 2 | Creates `public/fileadmin/`, `var/`, `translations/` directories |
| 3 | Copies `vendor/connetation/t3wtk-sitepackage/` → `packages/t3wtk-sitepackage/`, removes `.git` |
| 4 | Updates `composer.json`: sets `connetation/t3wtk-sitepackage: dev-local` |
| 5 | `composer update connetation/t3wtk-sitepackage` — rewires vendor symlink to `packages/t3wtk-sitepackage/` |
| 6 | `typo3 setup` — creates DB schema and admin user, then `extension:setup` + `cache:flush` |
| 7 | Builds sitepackage CSS (`npm install && npm run build`) — skipped if installed from dist archive |
| 8 | Removes the template's `.git` history and runs `git init` — your project starts from a clean slate |

### 3. Create your initial commit

setup-project wipes the template's `.git` history and reinitializes an empty repository, so nothing is committed yet. Make your first commit for the project:

```bash
git add -A
git commit -m "Initial commit"
```

From this point on, `packages/t3wtk-sitepackage/` is fully owned by this project. All customisations are committed here — no connection to the upstream `connetation/t3wtk-sitepackage` repository remains.

### 4. First login

Open `https://<project-name>.ddev.site/typo3`

- Username: `admin`
- Password: `Admin1234!` ← **change this immediately**

On a successful run, `setup-project` has already created and wired up two pages for you — there is no manual root-page setup step:

- **uid 1** — the root page, titled with the project name you entered during setup, marked as site root (`is_siteroot`)
- **uid 2** — "Seite nicht gefunden" ("Page not found"), active but hidden from menus, wired up as the 404 page via `config/sites/sitepackage/config.yaml`'s `errorHandling`

Log into the TYPO3 backend and both pages are already there, ready to fill with content.

## Customisation

### Sitepackage

The sitepackage at `packages/t3wtk-sitepackage/` is a full copy of `connetation/t3wtk-sitepackage`, freely editable:

- **CSS theme overrides**: edit `Resources/Private/Scss/_theme.scss`, then:
  ```bash
  ddev exec "cd /var/www/html/packages/t3wtk-sitepackage && npm run build"
  ```
  Or watch during development:
  ```bash
  ddev exec "cd /var/www/html/packages/t3wtk-sitepackage && npm run dev"
  ```

- **Fluid template overrides**: place templates in `Resources/Private/Templates/Page/`, `Partials/`, or `Layouts/` to override `conn_t3wtk_base` equivalents

- **TypoScript**: edit `Configuration/Sets/Sitepackage/setup.typoscript`

- **TCA overrides**: `Configuration/TCA/Overrides/`

### Site settings

Edit `config/sites/sitepackage/settings.yaml` to configure navbar style, footer layout, social links, branding, colours, etc.

All available settings with descriptions are documented in:
`vendor/connetation/t3wtk-base/Configuration/Sets/Base/settings.definitions.yaml`

## Deployment

### 1. Configure server credentials

Edit `deployer/hosts.yaml`:
```yaml
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
```

Edit `.gitlab-ci.yml` variables:
```yaml
PRODUCTION_HOST: "your-server.example.com"
PRODUCTION_USER: "your-deploy-user"
```

Add `SSH_PRIVATE_KEY_PRODUCTION` to GitLab CI/CD variables (project → Settings → CI/CD → Variables → Add variable):
- **Type**: `File` (not `Variable`) — the pipeline reads the key from the path GitLab injects (`ssh-add "$SSH_PRIVATE_KEY_PRODUCTION"`), not from the variable's value directly. `File` type also keeps the key out of the job's environment variable list, and it's required here anyway since a multi-line SSH key can't use GitLab's `Masked` option. Note: GitLab writes `File` type variables to disk as `0666`, so the pipeline runs `chmod 600` on it before `ssh-add` — otherwise `ssh-add` refuses the key with "UNPROTECTED PRIVATE KEY FILE".
- **Protected**: on, so the key is only exposed to pipelines running on protected branches (e.g. `main`) — make sure `main` is a protected branch before relying on this.
- Paste the private key exactly as generated (e.g. `~/.ssh/id_ed25519`), including the `-----BEGIN...-----`/`-----END...-----` lines.

### 2. Production `config/system/additional.php`

On the server, create this deployer shared file:
```php
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
```

### 3. Production `config/sites/sitepackage/config.local.yaml`

Also a deployer shared file — override the base URL for production:
```yaml
base: 'https://your-domain.com/'
```

## Pull from remote

Configure `.ddev/commands/host/pull-db.yaml` with your server details, then:

```bash
ddev pull-db master
ddev import-db --file=db_dump/master_YYYYMMDD_HHMM.sql.gz

ddev pull-config master
```

`pull-db` supports two environment types in `pull-db.yaml`:

- **Classic** (default, no `type:` needed) — SSHes in, reads DB credentials from the server's `config/system/settings.php`/`additional.php`, and runs `mysqldump` there.
- **Docker** (`type: docker`) — for servers where the app runs via Docker Compose and the DB has no port published on the host. SSHes in and runs `mariadb-dump` *inside* the `db` container via `docker compose exec`, using the container's own `MARIADB_USER`/`MARIADB_PASSWORD` env vars instead of a local TYPO3 config. Configure per environment:
  ```yaml
  environments:
    production:
      type: docker
      host: your-server.example.com
      user: docker-user
      port: 22
      path: /opt/project           # directory containing the compose file
      compose_file: compose.prod.yaml   # optional, defaults to compose.prod.yaml
      service: db                       # optional, defaults to db
  ```

## Content elements

```bash
ddev make-element
# or
ddev make-element "Hero Banner"
```

See [docs/create-new-element.md](docs/create-new-element.md) for prerequisites, prompts, generated files, and required post-scaffold steps.
