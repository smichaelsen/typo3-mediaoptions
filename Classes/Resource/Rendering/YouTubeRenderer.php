<?php
namespace Smichaelsen\Mediaoptions\Resource\Rendering;

use Smichaelsen\ShortcutParams\TypoScriptFrontendController;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\FlexFormService;

class YouTubeRenderer extends \TYPO3\CMS\Core\Resource\Rendering\YouTubeRenderer
{

    /**
     * Returns the priority of the renderer
     * @return int
     */
    public function getPriority()
    {
        return 5;
    }

    /**
     * Render for given File(Reference) html output
     *
     * @param FileInterface $file
     * @param int|string $width TYPO3 known format; examples: 220, 200m or 200c
     * @param int|string $height TYPO3 known format; examples: 220, 200m or 200c
     * @param array $options
     * @param bool $usedPathsRelativeToCurrentScript See $file->getPublicUrl()
     * @return string
     */
    public function render(FileInterface $file, $width, $height, array $options = null, $usedPathsRelativeToCurrentScript = false)
    {

        if ($file instanceof FileReference) {
            $orgFile = $file->getOriginalFile();
        } else {
            $orgFile = $file;
        }
        $videoId = $this->getOnlineMediaHelper($file)->getOnlineMediaId($orgFile);

        if (empty($options['additionalConfig']['referenceProperties']['mediaoptions'])) {
            // legacy options rendering
            $embedUrlParameterString = $this->getLegacyEmbedUrlParameterString($options);
        } else {
            $embedUrlParameters = $this->getEmbedUrlParameters($options['additionalConfig']['referenceProperties']['mediaoptions'], $videoId);
            // @todo: insert hook to modify $embedUrlParameters
            $embedUrlParameterString = http_build_query($embedUrlParameters);
        }

        $src = sprintf(
            '//www.youtube%s.com/embed/%s?%s',
            !empty($options['no-cookie']) ? '-nocookie' : '',
            $videoId,
            $embedUrlParameterString
        );

        $attributes = ['allowfullscreen'];
        if ((int)$width > 0) {
            $attributes[] = 'width="' . (int)$width . '"';
        }
        if ((int)$height > 0) {
            $attributes[] = 'height="' . (int)$height . '"';
        }
        if (is_object($GLOBALS['TSFE']) && $GLOBALS['TSFE']->config['config']['doctype'] !== 'html5') {
            $attributes[] = 'frameborder="0"';
        }
        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'poster', 'preload'] as $key) {
            if (!empty($options[$key])) {
                $attributes[] = $key . '="' . htmlspecialchars($options[$key]) . '"';
            }
        }

        return sprintf(
            '<iframe src="%s"%s></iframe>',
            $src,
            empty($attributes) ? '' : ' ' . implode(' ', $attributes)
        );
    }

    /**
     * @param array $options
     * @return string
     */
    protected function getLegacyEmbedUrlParameterString($options)
    {
        $urlParams = ['autohide=1'];
        if (!isset($options['controls']) || !empty($options['controls'])) {
            $urlParams[] = 'controls=2';
        }
        if (!empty($options['autoplay'])) {
            $urlParams[] = 'autoplay=1';
        }
        if (!empty($options['loop'])) {
            $urlParams[] = 'loop=1';
        }
        if (!isset($options['enablejsapi']) || !empty($options['enablejsapi'])) {
            $urlParams[] = 'enablejsapi=1&amp;origin=' . GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        }
        $urlParams[] = 'showinfo=' . (int)!empty($options['showinfo']);
        return join('&amp;', $urlParams);
    }

    /**
     * @param string $mediaoptions Flexform string
     * @param string $videoId
     * @return array
     */
    protected function getEmbedUrlParameters($mediaoptions, $videoId)
    {
        $embedUrlParameters = [];
        $flexFormData = GeneralUtility::makeInstance(ObjectManager::class)->get(FlexFormService::class)->convertFlexFormContentToArray($mediaoptions);
        if ((int)$flexFormData['autoplay'] === 1) {
            $embedUrlParameters['autoplay'] = 1;
        }
        if ((int)$flexFormData['ccLoadPolicy'] === 1) {
            $embedUrlParameters['cc_load_policy'] = 1;
        }
        if ((int)$flexFormData['enablejsapi'] === 1) {
            $embedUrlParameters['enablejsapi'] = 1;
        }
        if (!empty($flexFormData['end'])) {
            $embedUrlParameters['end'] = (int)$flexFormData['end'];
        }
        if ((int)$flexFormData['hideAnnotation'] === 1) {
            $embedUrlParameters['iv_load_policy'] = 3;
        }
        if ((int)$flexFormData['hideControls'] === 1) {
            $embedUrlParameters['controls'] = 0;
        }
        if ((int)$flexFormData['loop'] === 1) {
            $embedUrlParameters['loop'] = 1;
            $embedUrlParameters['playlist'] = $videoId;
        }
        if ((int)$flexFormData['modestBranding'] === 1) {
            $embedUrlParameters['modestbranding'] = 1;
        }
        if ((int)$flexFormData['passLanguageAlong'] === 1) {
            $languageIsoCode = $this->getTypoScriptFrontendController()->sys_language_isocode;
            if (!empty($languageIsoCode)) {
                $embedUrlParameters['hl'] = $languageIsoCode;
            }
        }
        if ((int)$flexFormData['preventFullScreen'] === 1) {
            $embedUrlParameters['fs'] = 0;
        }
        if ((int)$flexFormData['relatedVideos'] === 0) {
            $embedUrlParameters['rel'] = 0;
        }
        if (!empty($flexFormData['start'])) {
            $embedUrlParameters['start'] = (int)$flexFormData['start'];
        }
        if ($flexFormData['color'] === 'white') {
            $embedUrlParameters['color'] = 'white';
        }
        return $embedUrlParameters;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

}
