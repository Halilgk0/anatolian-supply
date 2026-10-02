<?php

/*
|--------------------------------------------------------------------------
| Vercel entry point
|--------------------------------------------------------------------------
|
| Vercel runs PHP functions from the api/ directory. Every request that is
| not a static file is routed here (see vercel.json) and handed to the
| regular Laravel front controller.
|
| PHP's built-in server, which runs the function, reports a path such as
| /README.md as the script name whenever a file with that name exists in
| the project. Laravel would then treat it as the site's base path and
| render the home page, so the script is pinned to /index.php here.
|
*/

$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/../public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require __DIR__.'/../public/index.php';
