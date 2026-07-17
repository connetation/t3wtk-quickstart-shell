# TYPO3 14 Project Template

A TYPO3 14 LTS project template built on [Connetation](https://connetation.at) base packages (`conn/base`, `conn/elements`, `conn/sitepackage`).

## Stack

- TYPO3 14 LTS
- PHP 8.3
- MariaDB 10.11
- Bootstrap 5 (via `conn/base`)
- Light/dark theme toggle
- DDEV for local development
- Deployer + GitLab CI/CD for deployment

## Project structure

```
.
├── .ddev/
│   ├── config.yaml                   DDEV settings (change name: per project)
│   └── commands/host/
│       ├── setup-project             Fresh TYPO3 install (run once after clone)
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
│   └── sitepackage/                  conn/sitepackage copied here, .git removed
│                                     Tracked by this project's git — customize freely
├── public/                           TYPO3 web root
│   ├── index.php
│   └── .htaccess
├── translations/                     Translation override files
├── .gitlab-ci.yml                    CI/CD pipeline (fill in server variables)
└── composer.json
```

## Starting a new project

### 1. Clone and rename

```bash
git clone <this-repo> my-project.com
cd my-project.com
```

Edit `.ddev/config.yaml` — change the `name:` to match your project:
```yaml
name: my-project
```

Edit `composer.json` — change the project name:
```json
"name": "my-vendor/my-project"
```

### 2. Start DDEV

```bash
ddev start
```

### 3. Run setup (once)

```bash
ddev setup-project
```

The command prompts for:

| Prompt | Default | Saved to |
|--------|---------|----------|
| Project name | Derived from DDEV name | `config/sites/…/config.yaml` websiteTitle + TYPO3 site name |
| DDEV subdomain | Current `name:` in `.ddev/config.yaml` | `.ddev/config.yaml`, DDEV restart if changed |
| Admin username | `admin` | TYPO3 admin account |
| Admin password | `Admin1234!` | TYPO3 admin account |
| Admin email | `admin@example.com` | TYPO3 admin account |

Then does the following in order:

| Step | What happens |
|------|-------------|
| 1 | `composer install` — installs all dependencies including `conn/sitepackage` from VCS |
| 2 | Creates `public/fileadmin/`, `var/`, `translations/` directories |
| 3 | Copies `vendor/conn/sitepackage/` → `packages/sitepackage/`, removes `.git` |
| 4 | Updates `composer.json`: removes VCS sitepackage repo, sets `conn/sitepackage: dev-local` |
| 5 | `composer update conn/sitepackage` — rewires vendor symlink to `packages/sitepackage/` |
| 6 | `typo3 setup` — creates DB schema and admin user, then `extension:setup` + `cache:flush` |
| 7 | Builds sitepackage CSS (`npm install && npm run build`) — skipped if installed from dist archive |
| 8 | Removes the template's `.git` history and runs `git init` — your project starts from a clean slate |

### 4. Create your initial commit

setup-project wipes the template's `.git` history and reinitializes an empty repository, so nothing is committed yet. Make your first commit for the project:

```bash
git add -A
git commit -m "Initial commit"
```

From this point on, `packages/sitepackage/` is fully owned by this project. All customisations are committed here — no connection to the upstream `conn/sitepackage` VCS remains.

### 5. First login and root page

Open `https://<project-name>.ddev.site/typo3`

- Username: `admin`
- Password: `Admin1234!` ← **change this immediately**

In the TYPO3 backend:
1. Create a page titled "Home"
2. In page properties → Behaviour → tick **"Use as Root Page"**
3. Go to **Site Management** → verify the site config is linked to your root page

## Customisation

### Sitepackage

The sitepackage at `packages/sitepackage/` is a full copy of `conn/sitepackage`, freely editable:

- **CSS theme overrides**: edit `Resources/Private/Scss/_theme.scss`, then:
  ```bash
  ddev exec "cd /var/www/html/packages/sitepackage && npm run build"
  ```
  Or watch during development:
  ```bash
  ddev exec "cd /var/www/html/packages/sitepackage && npm run dev"
  ```

- **Fluid template overrides**: place templates in `Resources/Private/Templates/Page/`, `Partials/`, or `Layouts/` to override `conn_base` equivalents

- **TypoScript**: edit `Configuration/Sets/Base/setup.typoscript`

- **TCA overrides**: `Configuration/TCA/Overrides/`

### Site settings

Edit `config/sites/sitepackage/settings.yaml` to configure navbar style, footer layout, social links, branding, colours, etc.

All available settings with descriptions are documented in:
`vendor/conn/base/Configuration/Sets/Base/settings.definitions.yaml`

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

Add `SSH_PRIVATE_KEY_PRODUCTION` to GitLab CI/CD variables (project → Settings → CI/CD → Variables).

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

## Content elements

```bash
ddev make-element
# or
ddev make-element "Hero Banner"
```
