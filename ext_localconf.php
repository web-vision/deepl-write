<?php

use WebVision\DeeplWrite\Hooks\PageRendererHook;
use WebVision\DeeplWrite\Hooks\WriteHook;

(static function (): void {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['deeplWrite'] =
        WriteHook::class;

    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['deeplwrite']
        = 'EXT:deepl_write/Configuration/RTE/DeeplWritePreset.yaml';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][1750182029]
        = PageRendererHook::class . '->renderPreProcess';
})();
