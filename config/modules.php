<?php

/**
 * Register application modules
 */
$application->registerModules(array(
    'frontend' => array(
        'className' => 'Bincg\Frontend\Module',
        'path' => __DIR__ . '/../apps/frontend/Module.php'
    ),
    'backend' => array(
        'className' => 'Bincg\Backend\Module',
        'path' => __DIR__ . '/../apps/backend/Module.php'
    )
));
