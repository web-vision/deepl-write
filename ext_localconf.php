<?php

use TYPO3\CMS\Core\Core\Environment;
use WebVision\DeeplWrite\Hooks\PageRendererHook;
use WebVision\DeeplWrite\Hooks\WriteHook;

defined('TYPO3') or die();

// Compatibility layer to provide autoloading for bundled libraries in classic mode
// instances prior to TYPO3 v14.3, which will use
// `extra.typo3/cms.Package.providesPackages` from the `composer.json` to add
// autoloading early in the TYPO3 bootstrap. Shipping the contrib library within the
// release artifact allows installing the extension in classic mode installations and
// still have the required library provided.
// @todo typo3/cms:>=14.3 Remove this compatibility layer providing contrib library autoloading.
if (!class_exists(\Org\Heigl\Hyphenator\Hyphenator::class)) {
    require Environment::getExtensionsPath() . '/deepl_write/contrib/Libraries/autoload.php';
}

(static function (): void {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['deeplWrite'] =
        WriteHook::class;

    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['deeplwrite']
        = 'EXT:deepl_write/Configuration/RTE/DeeplWritePreset.yaml';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][1750182029]
        = PageRendererHook::class . '->renderPreProcess';
})();
