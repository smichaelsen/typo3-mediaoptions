<?php
namespace Smichaelsen\Mediaoptions\Hooks;

class GetFlexformDataStructureHook
{

    /**
     * @param array $dataStructArray
     * @param array $conf
     * @param array $row
     * @param string $table
     * @param string $fieldName
     */
    public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, $row, $table, $fieldName)
    {
        if (strpos($conf['ds_pointerField'], ':') !== false) {
            // do it!
        }
    }

}
