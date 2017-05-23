<?php
require '../rollbar-init.php';

try {
    throw new \Exception('test exception');
} catch (\Exception $e) {
    Rollbar::log(Level::error(), $e);
}

// Message at level 'info'
Rollbar::log(Level::info(), 'testing info level');

// With extra data (3rd arg) and custom payload options (4th arg)
Rollbar::log(
    Level::info(),
    'testing extra data',
    array("some_key" => "some value") // key-value additional data
);

// If you want to check if logging with Rollbar was successful
$response = Rollbar::log(Level::info(), 'testing wasSuccessful()');
if (!$response->wasSuccessful()) {
    throw new \Exception('logging with Rollbar failed');
}

// Raises an E_NOTICE which will *not* be reported by the error handler
$foo = $bar;

// Will be reported by the exception handler
throw new \Exception('testing exception handler');
?>
