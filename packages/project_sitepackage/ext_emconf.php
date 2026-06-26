<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Project Sitepackage',
    'description' => 'Project-specific TYPO3 14 extension built on conn/sitepackage',
    'category' => 'templates',
    'author' => 'Your Name',
    'author_email' => 'your@email.com',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3'          => '14.0.0-14.99.99',
            'conn_base'      => '1.0.0-0.0.0',
            'conn_sitepackage' => '1.0.0-0.0.0',
        ],
    ],
];
