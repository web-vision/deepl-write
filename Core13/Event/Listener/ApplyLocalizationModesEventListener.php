<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Core13\Event\Listener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use WebVision\Deepl\Base\Event\GetLocalizationModesEvent;
use WebVision\Deepl\Base\Localization\LocalizationMode;

/**
 * Provides DeepL Write related localization modes by listening to the PSR-14
 * event {@see GetLocalizationModesEvent} dispatched by extension `deepl_base`
 * in {@see LocalizationController::dispatchGetLocalizationModesEvent()}.
 *
 * @internal and not part of public API.
 */
final class ApplyLocalizationModesEventListener
{
    #[AsEventListener(
        identifier: 'deeplwrite/deeplwrite-localization-modes-determine',
        after: 'deepl-base/determine-default-typo3-localization-modes',
    )]
    public function __invoke(GetLocalizationModesEvent $event): void
    {
        $writeMode = new LocalizationMode(
            identifier: 'deeplwrite',
            title: $event->getLanguageService()->sL('LLL:EXT:deepl_write/Resources/Private/Language/locallang.xlf:localize.educate.deeplwriteHeader'),
            description: $event->getLanguageService()->sL('LLL:EXT:deepl_write/Resources/Private/Language/locallang.xlf:localize.educate.deeplwrite'),
            icon: 'actions-localize-deepl-write',
            before: [],
            after: ['translate', 'copy'],
        );

        $event->getModes()->add($writeMode);
    }
}
