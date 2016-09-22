<?php

unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess::class]);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\Smichaelsen\Mediaoptions\Backend\Form\FormDataProvider\TcaFlexProcess::class] = [
    'depends' => [
        \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare::class,
    ],
];
