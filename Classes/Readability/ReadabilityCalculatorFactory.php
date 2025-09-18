<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability;

final class ReadabilityCalculatorFactory
{
    public function __construct(private readonly ReadabilityCalculatorRegistryInterface $registry)
    {
    }

    public function fromLanguage(string $language): ReadabilityCalculatorInterface
    {
        return $this->registry->findByLanguage($language);
    }
}
