<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\FieldType;

final class FieldTypeRegistry
{
    /**
     * @var array<string, FieldTypeInterface>
     */
    private static array $fieldTypes = [
        'input' => Input::class,
        'text' => Text::class,
    ];

    public static function getFieldProcessingTypeByRenderType(
        array $tcaConfig,
        string $table,
        string $fieldName,
        ?string $type = null
    ): FieldTypeInterface {
        $renderType = $tcaConfig['renderType'] ?? $tcaConfig['type'];
        return new self::$fieldTypes[$renderType](
            $tcaConfig,
            $table,
            $fieldName,
            $type
        );
    }
}
