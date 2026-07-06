<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsAlias(id: ReadabilityCalculatorRegistryInterface::class)]
final class ReadabilityCalculatorRegistry implements ReadabilityCalculatorRegistryInterface
{
    /**
     * @var array<ReadabilityCalculatorInterface>
     */
    private array $services = [];

    /**
     * @param iterable<ReadabilityCalculatorInterface> $calculators
     */
    public function __construct(
        #[AutowireIterator('deepl.readability')]
        iterable $calculators,
    ) {
        foreach ($calculators as $calculator) {
            $this->services[] = $calculator;
        }
    }

    public function findByLanguage(string $language): ReadabilityCalculatorInterface
    {
        $normalized = $this->normalizeLanguage($language);
        foreach ($this->services as $service) {
            if ($this->normalizeLanguage($service->getLanguage()) === $normalized) {
                return $service;
            }
        }
        throw new \InvalidArgumentException(
            sprintf('No readability calculator found for language "%s"', $language),
            1757686580
        );
    }

    /**
     * Reduce a language tag to its lowercased primary subtag, so that regional
     * variants such as "en-US", "en-GB" or "en_us" all resolve to the same
     * calculator (here: English), matching the two-letter locale codes that
     * CKEditor reports for the editing context.
     */
    private function normalizeLanguage(string $language): string
    {
        $language = str_replace('_', '-', strtolower(trim($language)));
        return explode('-', $language)[0];
    }
}
