<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Tests\Functional\Core14\Backend\Localization;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Backend\Localization\LocalizationHandlerInterface;
use TYPO3\CMS\Backend\Localization\LocalizationHandlerRegistry;
use TYPO3\CMS\Backend\Localization\LocalizationInstructions;
use TYPO3\CMS\Backend\Localization\LocalizationMode;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use WebVision\DeeplWrite\Core14\Backend\Localization\DeeplWriteLocalizationHandler;
use WebVision\DeeplWrite\Tests\Functional\Helper\AbstractDeepLTestCase;

/**
 * @group not-core-13 The DeepL Write localization handler and the
 *        LocalizationHandlerInterface only exist on TYPO3 v14.
 */
#[Group('not-core-13')]
final class DeeplWriteLocalizationHandlerTest extends AbstractDeepLTestCase
{
    protected array $testExtensionsToLoad = [
        'web-vision/deepl-base',
        'web-vision/deepl-write',
    ];

    protected array $configurationToUseInTestInstance = [
        'EXTENSIONS' => [
            'deepl_write' => [
                'apiKey' => 'mock_server',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../../../Fixtures/pages.csv');
        // Language 1 has a supported DeepL Write language configured, language 2 does not.
        $this->get(SiteWriter::class)->write('main', [
            'rootPageId' => 1,
            'base' => '/',
            'languages' => [
                [
                    'languageId' => 0,
                    'title' => 'English',
                    'base' => '/',
                    'locale' => 'en_US.UTF-8',
                ],
                [
                    'languageId' => 1,
                    'title' => 'Deutsch',
                    'base' => '/de/',
                    'locale' => 'de_DE.UTF-8',
                    'deeplWriteLanguage' => 'DE',
                ],
                [
                    'languageId' => 2,
                    'title' => 'Français',
                    'base' => '/fr/',
                    'locale' => 'fr_FR.UTF-8',
                ],
            ],
        ]);
    }

    #[Test]
    public function handlerIsRegisteredInHandlerRegistry(): void
    {
        $registry = $this->get(LocalizationHandlerRegistry::class);
        static::assertInstanceOf(LocalizationHandlerRegistry::class, $registry);
        static::assertTrue($registry->hasHandler('deeplwrite'));
        static::assertInstanceOf(DeeplWriteLocalizationHandler::class, $registry->getHandler('deeplwrite'));
    }

    #[Test]
    public function isNotAvailableForCopyMode(): void
    {
        $instructions = new LocalizationInstructions('pages', 1, 0, 1, LocalizationMode::COPY, []);
        static::assertFalse($this->getHandler()->isAvailable($instructions));
    }

    #[Test]
    public function isNotAvailableWhenSiteCannotBeDetermined(): void
    {
        // Page 999 is not part of any site configuration.
        $instructions = new LocalizationInstructions('pages', 999, 0, 1, LocalizationMode::TRANSLATE, []);
        static::assertFalse($this->getHandler()->isAvailable($instructions));
    }

    #[Test]
    public function isNotAvailableWhenTargetLanguageHasNoDeeplWriteLanguage(): void
    {
        // Target language 2 has no deeplWriteLanguage configured.
        $instructions = new LocalizationInstructions('pages', 1, 0, 2, LocalizationMode::TRANSLATE, []);
        static::assertFalse($this->getHandler()->isAvailable($instructions));
    }

    #[Test]
    public function isAvailableForTranslateModeWithConfiguredTargetLanguage(): void
    {
        // Target language 1 has deeplWriteLanguage "DE" configured.
        $instructions = new LocalizationInstructions('pages', 1, 0, 1, LocalizationMode::TRANSLATE, []);
        static::assertTrue($this->getHandler()->isAvailable($instructions));
    }

    private function getHandler(): LocalizationHandlerInterface
    {
        return $this->get(LocalizationHandlerRegistry::class)->getHandler('deeplwrite');
    }
}
