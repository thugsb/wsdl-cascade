<?php
require_once __DIR__ . '/vendor/autoload.php';

// The following two lines need to also be present in any file that is using Rollbar:
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;

require_once __DIR__ . '/_credentials.php';
// Installs global error and exception handlers
$rollbarConfig = array(
    // required
    'access_token' => $rollbarToken,
    // optional - environment name
    'environment' => 'production'//,
    // optional - path to directory your code is in. Used for linking stack traces.
    // 'root' => '/Users/brian/www/myapp'
);
Rollbar::init($rollbarConfig);
?>
