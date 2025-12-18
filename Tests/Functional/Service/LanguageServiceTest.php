<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Site\Entity\Site;
use WebVision\DeeplWrite\Service\LanguageService;
use WebVision\DeeplWrite\Tests\Functional\Helper\AbstractDeepLTestCase;

final class LanguageServiceTest extends AbstractDeepLTestCase
{
    protected array $coreExtensionsToLoad = [
        'typo3/cms-setup',
    ];

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

    #[Test]
    public function getTargetLanguageForRephrasingReturnsNullIfDeeplWriteLanguageDoesNotExistsInSiteConfig(): void
    {
        $site = new Site(
            identifier: 'dummy',
            rootPageId: 1,
            configuration: [
                'languages' => [
                    [
                        'languageId' => 0,
                        'enabled' => true,
                        'title' => 'English',
                        'base' => '/',
                        'locale' => 'en_US.UTF-8',
                        'navigationTitle' => 'English',
                        'flag' => 'us',
                        'typo3Language' => 'default',
                        'iso-639-1' => 'en',
                        'hreflang' => 'en-us',
                        'direction' => 'ltr',
                        'websiteTitle' => '',
                    ],
                    [
                        'languageId' => 1,
                        'enabled' => true,
                        'title' => 'Deutsch',
                        'base' => '/de/',
                        'locale' => 'de_DE.UTF-8',
                        'navigationTitle' => 'English',
                        'flag' => 'us',
                        'typo3Language' => 'de',
                        'iso-639-1' => 'de',
                        'hreflang' => 'de-de',
                        'direction' => 'ltr',
                        'websiteTitle' => '',
                    ],
                ],
            ],
            settings: null,
        );
        $targetSite = $this->get(LanguageService::class)->getTargetLanguageForRephrasing($site, 1);
        static::assertNull($targetSite);
    }

    #[Test]
    public function getTargetLanguageForRephasingReturnsNullForInvalidConfiguredDeeplWriteLanguage(): void
    {
        $site = new Site(
            identifier: 'dummy',
            rootPageId: 1,
            configuration: [
                'languages' => [
                    [
                        'languageId' => 0,
                        'enabled' => true,
                        'title' => 'English',
                        'base' => '/',
                        'locale' => 'en_US.UTF-8',
                        'navigationTitle' => 'English',
                        'flag' => 'us',
                        'typo3Language' => 'default',
                        'iso-639-1' => 'en',
                        'hreflang' => 'en-us',
                        'direction' => 'ltr',
                        'websiteTitle' => '',
                    ],
                    [
                        'languageId' => 1,
                        'enabled' => true,
                        'title' => 'Deutsch',
                        'base' => '/de/',
                        'locale' => 'de_DE.UTF-8',
                        'navigationTitle' => 'English',
                        'flag' => 'us',
                        'typo3Language' => 'de',
                        'iso-639-1' => 'de',
                        'hreflang' => 'de-de',
                        'direction' => 'ltr',
                        'websiteTitle' => '',
                        'deeplWriteLanguage' => 'RU',
                    ],
                ],
            ],
            settings: null,
        );
        $targetSite = $this->get(LanguageService::class)->getTargetLanguageForRephrasing($site, 1);
        static::assertNull($targetSite);
    }

    #[Test]
    public function getTargetLanguageForRephrasingReturnsValidConfiguredDeeplWriteLanguage(): void
    {
        $site = new Site(
            identifier: 'dummy',
            rootPageId: 1,
            configuration: [
                'languages' => [
                    [
                        'languageId' => 0,
                        'enabled' => true,
                        'title' => 'English',
                        'base' => '/',
                        'locale' => 'en_US.UTF-8',
                        'navigationTitle' => 'English',
                        'flag' => 'us',
                        'typo3Language' => 'default',
                        'iso-639-1' => 'en',
                        'hreflang' => 'en-us',
                        'direction' => 'ltr',
                        'websiteTitle' => '',
                    ],
                    [
                        'languageId' => 1,
                        'enabled' => true,
                        'title' => 'Deutsch',
                        'base' => '/de/',
                        'locale' => 'de_DE.UTF-8',
                        'navigationTitle' => 'English',
                        'flag' => 'us',
                        'typo3Language' => 'de',
                        'iso-639-1' => 'de',
                        'hreflang' => 'de-de',
                        'direction' => 'ltr',
                        'websiteTitle' => '',
                        'deeplWriteLanguage' => 'DE',
                    ],
                ],
            ],
            settings: null,
        );
        $targetSite = $this->get(LanguageService::class)->getTargetLanguageForRephrasing($site, 1);
        static::assertSame('DE', $targetSite);
    }
}
