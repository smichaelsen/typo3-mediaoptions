<?php

foreach (array_keys($GLOBALS['TCA']['tt_content']['columns']['image']['config']['foreign_types']) as $key) {
    $GLOBALS['TCA']['tt_content']['columns']['image']['config']['foreign_types'][$key]['showitem'] .= ',mediaoptions';
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('fluid_styled_content')) {
    foreach (array_keys($GLOBALS['TCA']['tt_content']['columns']['assets']['config']['foreign_types']) as $key) {
        $GLOBALS['TCA']['tt_content']['columns']['assets']['config']['foreign_types'][$key]['showitem'] .= ',mediaoptions';
    }
}
