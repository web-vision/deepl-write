<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\CMS\Fluid\View\StandaloneView;
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
final class CkEditorController
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ConfigurationInterface $configuration,
        private readonly DeeplService $deeplService,
        private readonly HtmlParser $htmlParser,
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
        $splittedText = $this->htmlParser->splitHtml($data['text']);
        foreach ($splittedText as $node => $text) {
            $optimizedText = $this->deeplService->rephraseText(
                $data['text'],
                null,
                RephraseWritingStyleDeepL::tryFrom($data['style']),
                RephraseToneDeepL::tryFrom($data['tone'])
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
        $renderingContext = GeneralUtility::makeInstance(RenderingContextFactory::class)->create(
            templatePathsArray: [
                'templateRootPaths' => ['EXT:deepl_write/Resources/Private/Backend/Templates/'],
                ]
        );
        $renderingContext->setRequest($request);
        $renderingContext->setControllerAction('Edit');
        $renderingContext->setControllerName('CkEditor');
        $view = GeneralUtility::makeInstance(StandaloneView::class, $renderingContext);
        $view->assignMultiple([
            'styles' => RephraseWritingStyleDeepL::cases(),
            'tones' => RephraseToneDeepL::cases(),
        ]);
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
        $response->getBody()->write($view->render());
        return $response;
    }
}
