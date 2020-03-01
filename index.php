<?php
use Sabre\HTTP;

require_once 'vendor/autoload.php';



// create request and response instances
$request = HTTP\Sapi::getRequest();
$response = new HTTP\Response();

// set default headers
$response->setHeader('Access-Control-Allow-Origin', '*');
$response->setHeader('Content-Type', 'application/json');

// parse .env file
$data = parse_env($request, $response);

// show errors only if DEBUG env var is set to TRUE
if ($_ENV['DEBUG']) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
} else {
    error_reporting(0);
}

// parse HTTP request
$data = parse_request($request, $response, $data);

// send email
send_email($request, $response, $data);
