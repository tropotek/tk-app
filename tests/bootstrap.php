<?php

// The Docker container sets a real APP_ENV=local environment variable, which
// ends up in $_SERVER before phpunit.xml's <php><env> overrides are applied
// (those only touch $_ENV/putenv()). Laravel's env() reads $_SERVER first, so
// without this the app never actually boots in the "testing" environment.
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'testing';

require __DIR__.'/../vendor/autoload.php';
