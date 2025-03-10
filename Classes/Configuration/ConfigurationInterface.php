<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Configuration;

use TYPO3\CMS\Core\SingletonInterface;

interface ConfigurationInterface extends SingletonInterface
{
    public function getApiKey(): string;
}
