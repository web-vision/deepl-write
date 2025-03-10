<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Configuration;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class Configuration implements ConfigurationInterface
{
    private string $apiKey;

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public function __construct()
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('deepl_write');
        $apiKey = $extensionConfiguration['apiKey'] ?? null;
        // fallback, if deepltranslate_core is installed and has a valid API key
        if (($apiKey === null || $apiKey === '') && ExtensionManagementUtility::isLoaded('deepltranslate_core')) {
            $deeplExtensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('deepltranslate_core');
            $apiKey = $deeplExtensionConfiguration['apiKey'] ?? null;
        }

        $this->apiKey = (string)($apiKey ?? '');
    }
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
