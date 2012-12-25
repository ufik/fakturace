<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

// Let bootstrap create Dependency Injection container.
$container = require __DIR__ . '/../app/bootstrap.php';

define('WWW_DIR',  __DIR__ . '/');
define('APP_DIR',  __DIR__ .'/../app');
define('LIBS_DIR', __DIR__ . '/../libs');

// Run application.
$container->application->run();
