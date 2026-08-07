<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WebVision\DeeplWrite\Readability\ReadabilityCalculatorInterface;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder): void {
    $containerBuilder->registerForAutoconfiguration(ReadabilityCalculatorInterface::class)->addTag('deepl.readability');
};
