<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass'][] = \Smichaelsen\Mediaoptions\Hooks\GetFlexformDataStructureHook::class;

/** @var \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry $rendererRegistry */
$rendererRegistry = \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::getInstance();
$rendererRegistry->registerRendererClass(\Smichaelsen\Mediaoptions\Resource\Rendering\YouTubeRenderer::class);
unset($rendererRegistry);
