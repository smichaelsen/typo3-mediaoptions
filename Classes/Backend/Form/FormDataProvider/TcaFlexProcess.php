<?php
namespace Smichaelsen\Mediaoptions\Backend\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaFlexProcess extends \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess
{

    /**
     * Take care of ds_pointerField and friends to determine the correct sub array within
     * TCA config ds.
     *
     * Gets extension identifier. Use second pointer field if it's value is not empty, "list" or "*",
     * else it must be a plugin and first one will be used.
     * This code basically determines the sub key of ds field:
     * config = array(
     *  ds => array(
     *    'aFlexConfig' => '<flexXml ...
     *     ^^^^^^^^^^^
     * $flexformIdentifier contains "aFlexConfig" after this operation.
     *
     * @todo: This method is only implemented half. It basically should do all the
     * @todo: pointer handling that is done within BackendUtility::getFlexFormDS() to $srcPointer.
     *
     * @param array $result Result array
     * @param string $fieldName Current handle field name
     * @return string Pointer
     */
    protected function getFlexIdentifier(array $result, $fieldName)
    {
        // @todo: Current implementation with the "list_type, CType" fallback is rather limited and customized for
        // @todo: tt_content, also it forces a ds_pointerField to be defined and a casual "default" sub array does not work
        $pointerFields = !empty($result['processedTca']['columns'][$fieldName]['config']['ds_pointerField'])
            ? $result['processedTca']['columns'][$fieldName]['config']['ds_pointerField']
            : 'list_type,CType';
        $pointerFields = GeneralUtility::trimExplode(',', $pointerFields);
        if (strpos($pointerFields[0], ':') !== false) {
            return $this->resolveForeignFieldFlexIdentifier($result, $fieldName, $pointerFields);
        }
        $flexformIdentifier = !empty($result['databaseRow'][$pointerFields[0]]) ? $result['databaseRow'][$pointerFields[0]] : '';
        if (!empty($result['databaseRow'][$pointerFields[1]])
            && $result['databaseRow'][$pointerFields[1]] !== 'list'
            && $result['databaseRow'][$pointerFields[1]] !== '*'
        ) {
            $flexformIdentifier = $result['databaseRow'][$pointerFields[1]];
        }
        if (empty($flexformIdentifier)) {
            $flexformIdentifier = 'default';
        }

        return $flexformIdentifier;
    }

    /**
     * @param array $result
     * @param string $fieldName
     * @param array $pointerFields
     * @return
     */
    protected function resolveForeignFieldFlexIdentifier($result, $fieldName, $pointerFields)
    {
        // This is a hack to gain advantage of the foreign field resultion that is done to the 'type' field because we want
        // the same feature for the flex pointer
        $pseudoResult = [
            'databaseRow' => $result['databaseRow'],
            'processedTca' => [
                'ctrl' => [
                    'type' => $pointerFields[0],
                ],
                'columns' => $result['processedTca']['columns'],
                'types' => [
                    '0' => ['showitem' => ''],
                ],
            ],
        ];
        foreach (array_keys($GLOBALS['TCA']['sys_file_reference']['columns'][$fieldName]['config']['ds']) as $possibleFlexFormKey) {
            $pseudoResult['processedTca']['types'][$possibleFlexFormKey] = ['showitem' => ''];
        }
        $pseudoResult = GeneralUtility::makeInstance(DatabaseRecordTypeValue::class)->addData($pseudoResult);
        return $pseudoResult['recordTypeValue'];
    }

}
