<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use TYPO3\CMS\Core\Information\Typo3Version;

return function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder) {
    $typo3Version = new Typo3Version();
    $majorVersion = $typo3Version->getMajorVersion();
    $services = $containerConfigurator->services();

    //==================================================================================================================
    // The default configuration: allow autowire and autoconfigure,
    // no need to make every class public.
    //==================================================================================================================
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private(); // "private" is the default and can safely be omitted
    //==================================================================================================================

    //==================================================================================================================
    // Define the location of the PHP sources of our extension.
    // In addition, exclude Extbase models that should never be used via DI.
    //==================================================================================================================
    $coreVersionSourcePath = sprintf(__DIR__ . '/../Core%d/', $majorVersion);
    if (is_dir($coreVersionSourcePath)) {
        $services->load(
            sprintf('WebVision\\DeeplWrite\\Core%d\\', $majorVersion),
            $coreVersionSourcePath,
        );
    }
    //==================================================================================================================
};
