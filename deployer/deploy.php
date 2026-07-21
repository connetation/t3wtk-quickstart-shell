<?php

namespace Deployer;

require_once '/tmp/vendor/deployer/deployer/recipe/common.php';
require_once '/tmp/vendor/deployer/recipes/recipe/rsync.php';

$sharedDirectories = [
    'public/fileadmin',
    'public/typo3temp',
    'var/charset',
    'var/lock',
    'var/log',
    'var/session',
    'var/transient',
    'translations',
];
set('shared_dirs', $sharedDirectories);

$sharedFiles = [
    'config/system/additional.php',
    'config/sites/sitepackage/config.local.yaml',
    'config/sites/sitepackage/settings.yaml',
];
set('shared_files', $sharedFiles);

$writeableDirectories = [
    'public/typo3temp'
];
set('writable_dirs', $writeableDirectories);

$exclude = [
    'var',
    '.gitignore',
    '.htaccess',
    '.git',
    '.ddev',
    '.gitlab-ci.yml',
    'additional.php',
    'additional.ddev.php',
    'auth.json',
    'Readme.rst',
    'Readme.txt',
    'Upgrading.rst',
    'Upgrading.txt',
    'README',
    '*.example',
    'Build',
    '.lando.yml',
    '.lando',
    '.platform',
    '.netrc',
    'dev-shared',
    '.gitlab-ci',
    '.bash*',
    'webpack*',
    'CONTRIBUTING.md',
    'babel.config.js',
    'browser-sync.config*',
    'deploy.php',
    'husky.config.js',
    'lint-staged.config.js',
    'package-lock.json',
    'package.json',
    'php.ini',
    'postcss.config.js',
    'prettier.config.js',
    'provisioning',
    'stylelint.config.js',
    'tsconfig.json',
    'symfony.lock',
    '.browserslistrc',
    '.deploy',
    '.env.lando',
    '.environment',
    '.eslintrc.js',
    '.eslintignore',
    '.husky',
    '.nvmrc',
    '.php-cs-fixer.php',
    '.platform.app.yaml',
    '.prettierignore',
    '.stylelintignore',
    '.sync',
    '.typo3.php_cs',
];
set('rsync', [
    'exclude' => array_merge($sharedDirectories, $sharedFiles, $exclude),
    'exclude-file' => false,
    'include' => [],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'avz',
    'options' => ['delete', 'chmod=D755,F644'],
    'timeout' => 300
]);

set('rsync_src', './');

inventory(__DIR__ . '/hosts.yaml');

set('php', '/usr/bin/php8.3');
set('bin_folder', 'vendor/bin/');

task('typo3', function () {
    run('cd {{release_path}}/ && {{php}} {{bin_folder}}typo3 extension:setup');
    run('cd {{release_path}}/ && {{php}} {{bin_folder}}typo3 language:update');
});

task('cache', function () {
    run('cd {{release_path}}/ && {{php}} {{bin_folder}}cachetool opcache:reset --fcgi={{socket}} --tmp-dir=/tmp');
    run('cd {{release_path}}/ && {{php}} {{bin_folder}}typo3 cache:flush');
});

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'rsync:warmup',
    'rsync',
    'deploy:shared',
    'typo3',
    'deploy:symlink',
    'cache',
    'cleanup'
]);
