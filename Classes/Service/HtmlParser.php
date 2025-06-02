<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Service;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMText;

/**
 * @todo This is just a proof of concept and has many lags at the moment, for example
 *       not supported inline tags like a or span. This has to be adjusted to deal
 *       correctly with inline values.
 */
final class HtmlParser
{
    public function splitHtml(string $value): array
    {
        // The template tag is necessary!
        // Having multiple same nodes without any wrapper confuses the DOMDocument
        // parser and creates the p tags inside p tags. Using template
        // as wrapper allows having only one base tag.
        // As template tag is official HTML5 standard and not used inside RTE,
        // this solution seems the best at the moment
        $value = sprintf('<template>%s</template>', str_replace(PHP_EOL, '', $value));
        $iterator = new DOMDocument();
        $iterator->loadHTML(
            $value,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOCDATA | LIBXML_NOEMPTYTAG | LIBXML_NOERROR | LIBXML_NONET | LIBXML_NOWARNING
        );
        return $this->xmlToArray($iterator->childNodes);
    }

    /**
     * For key description of the passed array:
     * @see HtmlParser::xmlToArray()
     *
     * @param array<non-empty-string, string> $processedValue
     */
    public function buildHtml(array $processedValue): string
    {
        $result = [];
        foreach ($processedValue as $htmlEntryPoint => $value) {
            $entryPointLevel = explode('>', $htmlEntryPoint);
            $result = $this->addToResult($result, $entryPointLevel, $value);
        }
        $domResult = new DOMDocument('1.0', 'UTF-8');

        $this->addToDomRecursive($domResult, $result);
        $generatedHtml = $domResult->saveHTML();
        return str_replace(['<template>', '</template>'], '', $generatedHtml);
    }

    /**
     * Returns the HTML flattened as DOM node > value
     *
     * Each DOM level is separated by `>`, while the internal count
     * of the node (1st element, 2nd element and so on) is put togeher
     * in the following structure: `<node type>|<number of the element in current level>,
     * for example, p|2 means 2nd element in current level and HTML tag <p></p>.
     *
     * @return array<non-empty-string, string>
     */
    private function xmlToArray(DOMNodeList $nodeList, string $parentNodeName = ''): array
    {
        $result = [];
        $i = 0;
        /** @var DOMNode $child */
        foreach ($nodeList as $node) {
            $nodeNameAndLevel = sprintf('%s%s|%d', $parentNodeName ? sprintf('%s>', $parentNodeName) : '', $node->nodeName, $i);
            if ($node->hasChildNodes()) {
                $result = array_merge(
                    $result,
                    $this->xmlToArray($node->childNodes, $nodeNameAndLevel)
                );
            } else {
                $result[$nodeNameAndLevel] = $node->nodeValue;
            }
            $i++;
        }
        return $result;
    }

    /**
     * Unflattens the processed array and makes it associative
     * for further processing
     */
    private function addToResult(array $result, $entryPointLevel, string $value): array
    {
        $currentEntryPointLevel = array_shift($entryPointLevel);
        if ($currentEntryPointLevel === null) {
            $result = [$value];
            return $result;
        }
        [$nodeType, $countInLevel] = explode('|', $currentEntryPointLevel);
        $result[$countInLevel][$nodeType] ??= [];
        $result[$countInLevel][$nodeType] = $this->addToResult($result[$countInLevel][$nodeType], $entryPointLevel, $value);

        return $result;
    }

    /**
     * Adds all found nodes recursively to the DOM
     */
    private function addToDomRecursive(DOMNode $parentNode, array $currentProcessing): void
    {
        foreach ($currentProcessing as $processingNode) {
            if (!is_array($processingNode)) {
                // last node, text only
                $parentNode->nodeValue = $processingNode;
                return;
            }
            $currentNodeType = array_keys($processingNode)[0];
            if ($currentNodeType === '#text') {
                $currentNode = new DOMText();
            } else {
                try {
                    $currentNode = new DOMElement($currentNodeType);
                } catch (\DOMException $e) {
                    // @todo add more DOMNode properties
                }
            }
            $parentNode->appendChild($currentNode);
            if (is_array($processingNode[$currentNodeType])) {
                $this->addToDomRecursive($currentNode, $processingNode[$currentNodeType]);
            }
        }
    }
}
