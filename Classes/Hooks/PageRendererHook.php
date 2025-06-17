<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Hooks;

use TYPO3\CMS\Core\Page\PageRenderer;

final class PageRendererHook
{
    /**
     * Ensure backend javascript module is required and loaded.
     *
     * @param array<string, mixed> $params
     */
    public function renderPreProcess(array $params, PageRenderer $pageRenderer): void
    {
        if ($pageRenderer->getApplicationType() === 'BE') {
            // For some reason, the labels are not available in JavaScript object `TYPO3.lang`. So we add them manually.
            $pageRenderer->addInlineLanguageLabelFile('EXT:deepl_write/Resources/Private/Language/locallang_cke.xlf');
        }
    }
}
