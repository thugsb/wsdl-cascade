<?php
require __DIR__ . '/vendor/autoload.php';
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;
require_once '_credentials.php';

// Installs global error and exception handlers
$config = array(
    // required
    'access_token' => $rollbarToken,
    // optional - environment name
    'environment' => 'production'//,
    // optional - path to directory your code is in. Used for linking stack traces.
    // 'root' => '/Users/brian/www/myapp'
);
Rollbar::init($config);
?>
