<?php
define('LARAVEL_START', microtime(true));

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/


/*$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
//解决跨域
$allowOrigin = [
    'http://127.0.0.1:8020',
    'http://localhost:8020',
    'http://localhost:9527',
];

if (in_array($origin, $allowOrigin)) {
    header('Access-Control-Allow-Origin:' . $origin);
}*/
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Credentials: true');  //是否支持cookie跨域
header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, X-ISAPI, X-Token");


require __DIR__.'/../vendor/autoload.php';



/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
