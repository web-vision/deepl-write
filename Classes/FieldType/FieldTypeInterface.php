<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\FieldType;

interface FieldTypeInterface
{
    public function __construct(
        array $configuration,
        string $table,
        string $fieldName,
        ?string $type
    );

    /**
     * @return array<array-key, string>
     */
    public function getTextForProcessing(int|string $value): array;

    /**
     * @param array<array-key, string> $processedText
     */
    public function getValueForDatabase(array $processedText): int|string;
}
