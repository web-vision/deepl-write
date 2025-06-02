<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Client;

use DeepL\DeepLClient;
use DeepL\DeepLClientOptions;
use DeepL\TranslatorOptions;
use TYPO3\CMS\Core\Http\Client\GuzzleClientFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\DeeplWrite\Configuration\ConfigurationInterface;
use WebVision\DeeplWrite\Exception\ApiKeyNotSetException;

final class ClientFactory
{
    /**
     * Copied from TranslatorOptions
     * for iterating over Client Configuration
     * @see TranslatorOptions::OPTIONS_KEYS
     */
    private const OPTIONS_KEYS = [
        TranslatorOptions::SERVER_URL,
        TranslatorOptions::HEADERS,
        TranslatorOptions::TIMEOUT,
        TranslatorOptions::MAX_RETRIES,
        TranslatorOptions::PROXY,
        TranslatorOptions::LOGGER,
        TranslatorOptions::HTTP_CLIENT,
        TranslatorOptions::SEND_PLATFORM_INFO,
        TranslatorOptions::APP_INFO,
    ];

    public function __construct(
        private readonly ConfigurationInterface $configuration
    ) {
    }

    public function buildDeeplClient(array $options = []): DeepLClient
    {
        if ($this->configuration->getApiKey() === '') {
            throw new ApiKeyNotSetException(
                'The DeepL API key is not set',
                1741343502
            );
        }
        if ($options === []) {
            $options[DeepLClientOptions::HTTP_CLIENT] = GeneralUtility::makeInstance(GuzzleClientFactory::class)->getClient();
            foreach (self::OPTIONS_KEYS as $option) {
                if ($options[DeepLClientOptions::HTTP_CLIENT]->getConfig($option) !== null) {
                    $options[$option] = $options[DeepLClientOptions::HTTP_CLIENT]->getConfig($option);
                }
            }
//            if (count($options) > 1) {
//                unset($options[DeepLClientOptions::HTTP_CLIENT]);
//            }
            return new DeepLClient($this->configuration->getApiKey(), $options);
        }
        return new DeepLClient($this->configuration->getApiKey(), $options);
    }
}
