<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\FieldType;

abstract class AbstractFieldType implements FieldTypeInterface
{
    public final function __construct(
        protected readonly array $configuration,
        protected readonly string $table,
        protected readonly string $fieldName,
        protected readonly ?string $type = null
    ) {}
}
