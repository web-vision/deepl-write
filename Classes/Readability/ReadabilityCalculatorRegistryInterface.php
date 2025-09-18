<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability;

interface ReadabilityCalculatorRegistryInterface
{
    public function findByLanguage(string $language): ReadabilityCalculatorInterface;
}
