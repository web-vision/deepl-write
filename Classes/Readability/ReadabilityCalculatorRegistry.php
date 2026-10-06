<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability;

final class ReadabilityCalculatorRegistry implements ReadabilityCalculatorRegistryInterface
{
    /**
     * @var array<ReadabilityCalculatorInterface>
     */
    private array $services;
    public function __construct(iterable $calculators)
    {
        foreach ($calculators as $calculator) {
            $this->services[] = $calculator;
        }
    }

    public function findByLanguage(string $language): ReadabilityCalculatorInterface
    {
        foreach ($this->services as $service) {
            if ($service->getLanguage() === $language) {
                return $service;
            }
        }
        throw new \InvalidArgumentException(
            sprintf('No service found for langauge "%s"', $language),
            1757686580
        );
    }
}
