<?php

use WebVision\DeeplWrite\Hooks\PageRendererHook;
use WebVision\DeeplWrite\Hooks\WriteHook;

(static function (): void {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['deeplWrite'] =
        WriteHook::class;
    // Register the presets
    // override default presets from EXT:rte_ckeditor
    // by using them as import and add `deeplwrite`
    // respect overrides by other extensions beside EXT:rte_ckeditor
    if (
        empty($GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'])
        || $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] === 'EXT:rte_ckeditor/Configuration/RTE/Default.yaml'
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:deepl_write/Configuration/RTE/Default.yaml';
    }
    if (
        empty($GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['minimal'])
        || $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['minimal'] === 'EXT:rte_ckeditor/Configuration/RTE/Minimal.yaml'
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['minimal'] = 'EXT:deepl_write/Configuration/RTE/Minimal.yaml';
    }
    if (
        empty($GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['full'])
        || $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['full'] === 'EXT:rte_ckeditor/Configuration/RTE/Full.yaml'
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['full'] = 'EXT:deepl_write/Configuration/RTE/Full.yaml';
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][1750182029]
        = PageRendererHook::class . '->renderPreProcess';
})();
