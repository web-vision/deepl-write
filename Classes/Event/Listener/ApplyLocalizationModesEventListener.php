<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Event\Listener;

use TYPO3\CMS\Core\Information\Typo3Version;
use WebVision\Deepl\Base\Event\GetLocalizationModesEvent;
use WebVision\Deepl\Base\Localization\LocalizationMode;

/**
 * Provides DeepL Write related localization modes by listening to the PSR-14
 * event {@see GetLocalizationModesEvent} dispatched by extension `deepl_base`
 * in {@see LocalizationController::dispatchGetLocalizationModesEvent()}.
 */
final class ApplyLocalizationModesEventListener
{
    public function __invoke(GetLocalizationModesEvent $event): void
    {
        $majorVersion = (new Typo3Version())->getMajorVersion();
        $writeMode = new LocalizationMode(
            identifier: 'deeplwrite',
            title: $event->getLanguageService()->sL('LLL:EXT:deepl_write/Resources/Private/Language/locallang.xlf:localize.educate.deeplwriteHeader'),
            description: $event->getLanguageService()->sL('LLL:EXT:deepl_write/Resources/Private/Language/locallang.xlf:localize.educate.deeplwrite'),
            icon: ($majorVersion === 13 ? 'actions-localize-deepl-13' : 'actions-localize-deepl'),
            before: [],
            after: ['translate', 'copy'],
        );

        $event->getModes()->add($writeMode);
    }
}
