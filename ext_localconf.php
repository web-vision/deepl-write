<?php

(static function (): void {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['deeplWrite'] =
        \WebVision\DeeplWrite\Hooks\WriteHook::class;
})();
