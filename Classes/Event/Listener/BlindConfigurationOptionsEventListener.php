<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Event\Listener;

use TYPO3\CMS\Lowlevel\Event\ModifyBlindedConfigurationOptionsEvent;

final class BlindConfigurationOptionsEventListener
{
    public function __invoke(ModifyBlindedConfigurationOptionsEvent $event): void
    {
        $options = $event->getBlindedConfigurationOptions();
        if ($event->getProviderIdentifier() === 'confVars') {
            $options['TYPO3_CONF_VARS']['EXTENSIONS']['deepl_write']['apiKey'] = '******';
        }
        $event->setBlindedConfigurationOptions($options);
    }
}
