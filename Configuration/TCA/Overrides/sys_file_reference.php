<?php

$GLOBALS['TCA']['sys_file_reference']['columns']['mediaoptions'] = [
    'label' => 'Options',
    'config' => [
        'type' => 'flex',
        'ds_pointerField' => 'uid_local:mime_type',
        'ds' => [
            'video/youtube' => 'FILE:EXT:mediaoptions/Configuration/FlexForms/YouTube.xml',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_file_reference', 'mediaoptions');
