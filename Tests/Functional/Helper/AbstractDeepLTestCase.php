<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Tests\Functional\Helper;

use DeepL\DeepLClientOptions;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
abstract class AbstractDeepLTestCase extends FunctionalTestCase
{

    /**
     * @var string
     */
    protected $authKey = 'mock_server';

    /**
     * @var string|false
     */
    protected $serverUrl = false;

    /**
     * @var string|false
     */
    protected $proxyUrl = false;

    protected bool $isMockServer = false;

    protected bool $isMockProxyServer = false;

    protected ?string $sessionNoResponse = null;

    protected ?string $session429Count = null;
    protected ?string $sessionInitCharacterLimit = null;

    protected ?string $sessionInitDocumentLimit = null;

    protected ?string $sessionInitTeamDocumentLimit = null;

    protected ?string $sessionDocFailure = null;

    protected ?int $sessionDocQueueTime = null;

    protected ?int $sessionDocTranslateTime = null;

    protected ?bool $sessionExpectProxy = null;

    protected function setUp(): void
    {
        $this->serverUrl = getenv('DEEPL_SERVER_URL');
        $this->proxyUrl = getenv('DEEPL_PROXY_URL');
        $this->isMockServer = getenv('DEEPL_MOCK_SERVER_PORT') !== false;
        $this->isMockProxyServer = $this->isMockServer && getenv('DEEPL_MOCK_PROXY_SERVER_PORT') !== false;

        if ($this->isMockServer) {
            $this->authKey = 'mock_server';
            if ($this->serverUrl === false) {
                throw new \Exception('DEEPL_SERVER_URL environment variable must be set if using a mock server');
            }
        } else {
            if (getenv('DEEPL_AUTH_KEY') === false) {
                throw new \Exception('DEEPL_AUTH_KEY environment variable must be set unless using a mock server');
            }
            $this->authKey = getenv('DEEPL_AUTH_KEY');
        }
        parent::setUp();
        $this->instantiateMockServerClient();
    }

    private function makeSessionName(): string
    {
        return sprintf('%s/%s', self::getInstanceIdentifier(), StringUtility::getUniqueId());
    }

    /**
     * @return array<string, mixed>
     */
    private function sessionHeaders(): array
    {
        $headers = [];
        if ($this->sessionNoResponse !== null) {
            $headers['mock-server-session-no-response-count'] = (string)($this->sessionNoResponse);
        }
        if ($this->session429Count !== null) {
            $headers['mock-server-session-429-count'] = (string)($this->session429Count);
        }
        if ($this->sessionInitCharacterLimit !== null) {
            $headers['mock-server-session-init-character-limit'] = (string)($this->sessionInitCharacterLimit);
        }
        if ($this->sessionInitDocumentLimit !== null) {
            $headers['mock-server-session-init-document-limit'] = (string)($this->sessionInitDocumentLimit);
        }
        if ($this->sessionInitTeamDocumentLimit !== null) {
            $headers['mock-server-session-init-team-document-limit'] = (string)($this->sessionInitTeamDocumentLimit);
        }
        if ($this->sessionDocFailure !== null) {
            $headers['mock-server-session-doc-failure'] = (string)($this->sessionDocFailure);
        }
        if ($this->sessionDocQueueTime !== null) {
            $headers['mock-server-session-doc-queue-time'] = (string)($this->sessionDocQueueTime * 1000);
        }
        if ($this->sessionDocTranslateTime !== null) {
            $headers['mock-server-session-doc-translate-time'] = (string)($this->sessionDocTranslateTime * 1000);
        }
        if ($this->sessionExpectProxy !== null) {
            $headers['mock-server-session-expect-proxy'] = $this->sessionExpectProxy ? '1' : '0';
        }
        $headers['mock-server-session'] = $this->makeSessionName();
        return $headers;
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function instantiateMockServerClient(array $options = []): void
    {
        $mergedOptions = array_replace(
            [DeepLClientOptions::HEADERS => $this->sessionHeaders()],
            $options
        );
        if ($this->serverUrl !== false) {
            $mergedOptions[DeepLClientOptions::SERVER_URL] = $this->serverUrl;
        }

        ArrayUtility::mergeRecursiveWithOverrule(
            $GLOBALS['TYPO3_CONF_VARS']['HTTP'],
            $mergedOptions
        );
    }
}
