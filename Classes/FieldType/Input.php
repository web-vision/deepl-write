<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\FieldType;

final class Input extends AbstractFieldType
{
    public function getTextForProcessing(
        string|int $value
    ): array {
        return [$value];
    }

    public function getValueForDatabase(array $processedText): int|string
    {
        return implode('', $processedText);
    }
}
