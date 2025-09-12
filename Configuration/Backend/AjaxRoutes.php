<?php

use WebVision\DeeplWrite\Controller\CkEditorController;
use WebVision\DeeplWrite\Controller\ReadabilityController;

return [
    'deeplwrite_ckeditor_configuration' => [
        'path' => '/deepl-write/ckeditor/configuration',
        'target' => CkEditorController::class . '::deeplConfiguredAction',
        'methods' => ['GET'],
    ],
    'deeplwrite_ckeditor_optimize' => [
        'path' => '/deepl-write/ckeditor/optimize',
        'target' => CkEditorController::class . '::optimizeTextAction',
        'methods' => ['POST'],
    ],
    'deeplwrite_ckeditor_edit' => [
        'path' => '/deepl-write/ckeditor/edit',
        'target' => CkEditorController::class . '::getEditMaskAction',
        'methods' => ['GET'],
    ],
    'deeplwrite_readability' => [
        'path' => '/deepl-write/readability/calculate',
        'target' => ReadabilityController::class . '::calculate',
        'methods' => ['post'],
    ],
];
