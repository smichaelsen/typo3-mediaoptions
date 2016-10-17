<?php
namespace Smichaelsen\Mediaoptions\Hooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class GetFlexformDataStructureHook
{

    /**
     * @param array $dataStructArray
     * @param array $conf
     * @param array $row
     * @param string $table
     */
    public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, $row, $table)
    {
        if (strpos($conf['ds_pointerField'], ':') !== false) {
            list($localField, $foreignField) = explode(':', $conf['ds_pointerField']);
            $foreignTable = $this->getForeignTable($table, $localField);
            $foreignUid = $row[$localField];
            if (!MathUtility::canBeInterpretedAsInteger($foreignUid)) {
                list($foreignUid) = explode('|', $foreignUid);
                $foreignUid = str_replace($foreignTable . '_', '', $foreignUid);
            }
            $foreignRecord = BackendUtility::getRecord($foreignTable, $foreignUid, $foreignField);
            if (!is_array($foreignRecord)) {
                return;
            }
            $foreignConf = $conf;
            $foreignConf['ds_pointerField'] = $foreignField;
            $dataStructArray = BackendUtility::getFlexFormDS($foreignConf, $foreignRecord, $foreignTable, $foreignField);
        }
    }

    /**
     * @param $table
     * @param $localField
     * @return string
     */
    protected function getForeignTable($table, $localField)
    {
        $fieldConfig = $GLOBALS['TCA'][$table]['columns'][$localField]['config'];
        if ($fieldConfig['type'] === 'group') {
            $allowedTables = explode(',', $fieldConfig['allowed']);
            return $allowedTables[0];
        } elseif ($fieldConfig['type'] === 'select') {
            return $fieldConfig['foreign_table'];
        }
        return '';
    }

}
