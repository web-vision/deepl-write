<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use WebVision\DeeplWrite\Readability\ReadabilityCalculatorFactory;

final class ReadabilityController
{
    public function __construct(private readonly ReadabilityCalculatorFactory $factory)
    {
    }

    public function calculate(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $readabilityCalculator = $this->factory->fromLanguage($data['language']);
        $readabilityResult = $readabilityCalculator->calculateReadability(strip_tags($data['text'] ?? ''));
        return new JsonResponse($readabilityResult->jsonSerialize());
    }
}
