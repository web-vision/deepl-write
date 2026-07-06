<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Core\Http\JsonResponse;
use WebVision\DeeplWrite\Readability\ReadabilityCalculatorFactory;

/**
 * @internal
 * This class is meant to be used within the DeepL write extension and therefore
 * no public API. Endpoints can change without further information.
 */
#[AsController]
final class ReadabilityController
{
    public function __construct(
        private readonly ReadabilityCalculatorFactory $factory,
    ) {
    }

    public function calculate(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $data = is_array($data) ? $data : [];
        $language = (string)($data['language'] ?? '');
        $text = strip_tags((string)($data['text'] ?? ''));

        try {
            $result = $this->factory->fromLanguage($language)->calculateReadability($text);
        } catch (\InvalidArgumentException) {
            // No readability calculator is registered for the requested language,
            // or the text contained no countable words. Report "no score" instead
            // of failing the request, so the editor overlay keeps working.
            return new JsonResponse(['score' => null]);
        }

        return new JsonResponse($result->jsonSerialize());
    }
}
