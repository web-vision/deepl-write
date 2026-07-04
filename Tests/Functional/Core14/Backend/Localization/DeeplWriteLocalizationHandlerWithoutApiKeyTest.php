<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Tests\Functional\Core14\Backend\Localization;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Backend\Localization\LocalizationHandlerRegistry;
use TYPO3\CMS\Backend\Localization\LocalizationInstructions;
use TYPO3\CMS\Backend\Localization\LocalizationMode;
use WebVision\DeeplWrite\Tests\Functional\Helper\AbstractDeepLTestCase;

/**
 * @group not-core-13 The DeepL Write localization handler and the
 *        LocalizationHandlerInterface only exist on TYPO3 v14.
 */
#[Group('not-core-13')]
final class DeeplWriteLocalizationHandlerWithoutApiKeyTest extends AbstractDeepLTestCase
{
    protected array $testExtensionsToLoad = [
        'web-vision/deepl-base',
        'web-vision/deepl-write',
    ];

    // No apiKey configured on purpose: the handler must not be offered.
    protected array $configurationToUseInTestInstance = [
        'EXTENSIONS' => [
            'deepl_write' => [
                'apiKey' => '',
            ],
        ],
    ];

    #[Test]
    public function isNotAvailableWithoutApiKey(): void
    {
        $handler = $this->get(LocalizationHandlerRegistry::class)->getHandler('deeplwrite');
        $instructions = new LocalizationInstructions('pages', 1, 0, 1, LocalizationMode::TRANSLATE, []);
        static::assertFalse($handler->isAvailable($instructions));
    }
}
