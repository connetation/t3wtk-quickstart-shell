# TYPO3 14 Project Template

A TYPO3 14 LTS project template built on [Connetation](https://connetation.at) base packages.

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
├── .ddev/                        DDEV configuration + custom commands
│   └── commands/host/
│       ├── setup-project         Fresh TYPO3 install
│       ├── make-element          Scaffold content elements
│       ├── pull-db               Download DB from remote
│       └── pull-config           Sync site config from remote
├── config/
│   ├── sites/sitepackage/        Site configuration
│   │   ├── config.yaml           Routes, languages, dependencies
│   │   └── settings.yaml         Theme, footer, branding settings
│   └── system/                   DB credentials (gitignored)
├── deployer/deploy.php           Deployer config (update server credentials)
├── packages/
│   └── project_sitepackage/      Project-specific TYPO3 extension
│       ├── Configuration/Sets/   Site set (depends on conn/sitepackage)
│       ├── Resources/Private/Scss/ Theme overrides
│       └── Resources/Public/Css/  Compiled CSS
├── public/                       TYPO3 web root (docroot)
└── composer.json
```

## Starting a new project

### 1. Clone and rename

```bash
git clone <this-repo> my-project
cd my-project
# Update .ddev/config.yaml: change `name:` to your project name
# Update composer.json: change `name:` to your vendor/project
```

### 2. Start DDEV

```bash
ddev start
```

### 3. Run setup

```bash
ddev setup-project
```

This installs Composer dependencies, creates the TYPO3 database schema and admin user.

### 4. First login

Open `https://<project-name>.ddev.site/typo3`

- Username: `admin`
- Password: `Admin1234!` ← **Change this immediately**

### 5. Create root page

In the TYPO3 backend:
1. Create a page titled "Home"
2. In page properties → Behaviour → tick **"Use as Root Page"**
3. Go to **Site Management** and verify the site config is linked

### 6. Build sitepackage styles

```bash
ddev exec "cd /var/www/html/packages/project_sitepackage && npm install && npm run build"
```

Or during active development with watch:
```bash
ddev exec "cd /var/www/html/packages/project_sitepackage && npm run dev"
```

## Customisation

### Rename the sitepackage

The local extension lives in `packages/project_sitepackage/`. To rename it:

1. Rename the directory: `mv packages/project_sitepackage packages/my_sitepackage`
2. Update `packages/my_sitepackage/composer.json` → `name` and `extra.typo3/cms.extension-key`
3. Update `packages/my_sitepackage/ext_emconf.php` → title / author
4. Update `packages/my_sitepackage/Configuration/Sets/Sitepackage/setup.typoscript` → replace `project_sitepackage` with `my_sitepackage`
5. Update `config/sites/sitepackage/config.yaml` → `dependencies` if you renamed the set
6. Run `ddev composer update`

### Theme settings

Edit `config/sites/sitepackage/settings.yaml` to configure navbar style, footer layout, social links, branding, etc.

All available settings with descriptions are in:
`vendor/conn/base/Configuration/Sets/Base/settings.definitions.yaml`

### SCSS theme overrides

Edit `packages/project_sitepackage/Resources/Private/Scss/_theme.scss` to override CSS custom properties.

## Deployment

### 1. Configure server

Edit `deployer/deploy.php`:
```php
host('main')
    ->hostname('your-server.example.com')
    ->user('your-deploy-user')
    ->set('deploy_path', '/path/to/deploy')
    ->set('socket', '/var/run/php/php8.3-fpm.sock');
```

### 2. Configure GitLab CI

Update `.gitlab-ci.yml` variables:
```yaml
PRODUCTION_HOST: "your-server.example.com"
PRODUCTION_USER: "your-deploy-user"
```

Add `SSH_PRIVATE_KEY_PRODUCTION` to your GitLab CI/CD variables (project → Settings → CI/CD → Variables).

### 3. Production TYPO3 config

On the production server create `config/system/additional.php` (this is a deployer shared file):
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
$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'your-64-char-encryption-key';
```

## Pull from remote

Once `pull-db.yaml` is configured with your server details:

```bash
# Pull database
ddev pull-db master
ddev import-db --file=db_dump/master_YYYYMMDD_HHMM.sql.gz

# Pull site config
ddev pull-config master
```

## Content elements

Scaffold a new content element (requires `conn/elements`):

```bash
ddev make-element
# or
ddev make-element "Hero Banner"
```
