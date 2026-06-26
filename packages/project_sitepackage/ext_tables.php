<?php

declare(strict_types=1);

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '@import EXT:project_sitepackage/Configuration/TsConfig/Page/*.tsconfig'
);
