<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Event\Listener;

use WebVision\Deepl\Base\Event\LocalizationProcessPrepareDataHandlerCommandMapEvent;

final class ProcessLocalizationModeEventListener
{
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
