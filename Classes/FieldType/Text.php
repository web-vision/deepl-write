<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\FieldType;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\DeeplWrite\Service\HtmlParser;

final class Text extends AbstractFieldType
{
    private ?HtmlParser $htmlParser = null;

    public function getTextForProcessing(
        string|int $value
    ): array {
        if ($this->isRteField()) {
            $processing = $this->getHtmlParser()->splitHtml($value);
        } else {
            $processing = explode("\n", $value);
        }
        return $processing;
    }

    public function getValueForDatabase(array $processedText): string
    {
        if ($this->isRteField()) {
            return $this->getHtmlParser()->buildHtml($processedText);
        }
        return implode("\n", $processedText);
    }

    private function isRteField(): bool
    {
        // FIRST check, if a type-based configuration is set
        // and richtext is enabled OR disabled there
        // as columnOverride always beats the default column configuration
        if (($GLOBALS['TCA'][$this->table]['types'][$this->type]['columnsOverrides'][$this->fieldName]['config']['enableRichtext'] ?? null) !== null) {
            return (bool)$GLOBALS['TCA'][$this->table]['types'][$this->type]['columnsOverrides'][$this->fieldName]['config']['enableRichtext'];
        }

        // if no columnOverride is found, check the value inside the default configuration
        return (bool)($this->configuration['enableRichtext'] ?? false);
    }

    private function getHtmlParser(): HtmlParser
    {
        if ($this->htmlParser === null) {
            $this->htmlParser = GeneralUtility::makeInstance(HtmlParser::class);
        }
        return $this->htmlParser;
    }
}
