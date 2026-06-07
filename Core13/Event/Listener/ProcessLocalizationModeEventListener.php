<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Core13\Event\Listener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use WebVision\Deepl\Base\Event\LocalizationProcessPrepareDataHandlerCommandMapEvent;

/**
 * @internal and not part of public API.
 */
final class ProcessLocalizationModeEventListener
{
    #[AsEventListener(
        identifier: 'deeplwrite/deeplwrite-localization-modes-process',
        after: 'deepl-base/process-default-typo3-localization-modes',
    )]
    public function __invoke(LocalizationProcessPrepareDataHandlerCommandMapEvent $event): void
    {
        // @todo Consider to drop `deepltranslateauto` mode.
        if ($event->getAction() !== 'deeplwrite'
            || !$event->getLocalizationModes()->hasIdentifier($event->getAction())
        ) {
            // Not responsible, early return.
            return;
        }
        $cmd = $event->getCmd();
        foreach ($event->getUidList() as $currentUid) {
            $cmd['tt_content'][$currentUid] = [
                // Both modes are handled by the same custom DataHandler command
                'deeplwrite' => $event->getDestLanguageId(),
            ];
        }
        $event->setCmd($cmd);
    }
}
