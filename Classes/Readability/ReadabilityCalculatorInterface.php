<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability;

use WebVision\DeeplWrite\Readability\Result\ReadabilityResult;

interface ReadabilityCalculatorInterface
{
    public function getLanguage(): string;
    public function calculateReadability(string $text): ReadabilityResult;
}
