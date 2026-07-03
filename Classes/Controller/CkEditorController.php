<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use WebVision\DeeplWrite\Configuration\ConfigurationInterface;
use WebVision\DeeplWrite\Domain\Enum\RephraseToneDeepL;
use WebVision\DeeplWrite\Domain\Enum\RephraseWritingStyleDeepL;
use WebVision\DeeplWrite\Service\DeeplService;
use WebVision\DeeplWrite\Service\HtmlParser;

/**
 * @internal
 * This class is meant to be used within the DeepL write extension and therefore
 * no public API. Endpoints can change without further information.
 */
#[AsController]
final class CkEditorController
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ConfigurationInterface $configuration,
        private readonly DeeplService $deeplService,
        private readonly HtmlParser $htmlParser,
        private readonly ViewFactoryInterface $viewFactory,
    ) {
    }

    public function deeplConfiguredAction(ServerRequestInterface $request): ResponseInterface
    {
        $configured = true;
        if ($this->configuration->getApiKey() === '') {
            $configured = false;
        }
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(
            json_encode(['configured' => $configured], JSON_THROW_ON_ERROR),
        );
        return $response;
    }

    public function optimizeTextAction(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $data = is_array($data) ? $data : [];
        $text = (string)($data['text'] ?? '');
        $style = (string)($data['style'] ?? '');
        $tone = (string)($data['tone'] ?? '');
        $splittedText = $this->htmlParser->splitHtml($text);
        foreach ($splittedText as $node => $value) {
            $optimizedText = $this->deeplService->rephraseText(
                $text,
                null,
                RephraseWritingStyleDeepL::tryFrom($style),
                RephraseToneDeepL::tryFrom($tone)
            );
            $splittedText[$node] = $optimizedText;
        }
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(
            json_encode(['result' => $this->htmlParser->buildHtml($splittedText)], JSON_THROW_ON_ERROR),
        );
        return $response;
    }

    public function getEditMaskAction(ServerRequestInterface $request): ResponseInterface
    {
        $majorVersion = (new Typo3Version())->getMajorVersion();
        $view = $this->viewFactory->create(new ViewFactoryData(
            templateRootPaths: [
                sprintf('EXT:deepl_write/Resources/Private/Core%d/Backend/Templates/', $majorVersion),
            ],
            request: $request,
        ));
        $view->assignMultiple([
            'styles' => RephraseWritingStyleDeepL::cases(),
            'tones' => RephraseToneDeepL::cases(),
        ]);
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
        $response->getBody()->write($view->render('CkEditor/Edit'));
        return $response;
    }
}
