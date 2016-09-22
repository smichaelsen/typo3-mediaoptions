<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mediaoptions',
    'description' => 'Provide options for TYPO3 media files, depending on their type.',
    'category' => 'fe',
    'state' => 'stable',
    'author' => 'Sebastian Michaelsen',
    'author_email' => '',
    'author_company' => 'app-zap',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.11-7.6.99',
        ],
    ],
];
