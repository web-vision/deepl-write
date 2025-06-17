<?php

use WebVision\DeeplWrite\Controller\CkEditorController;

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
];
