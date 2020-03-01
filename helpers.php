<?php

use Sabre\HTTP;

require_once 'vendor/autoload.php';


function json_response(HTTP\RequestInterface $request, HTTP\ResponseInterface $response, $status_code, $key, $message)
{
    $response->setStatus($status_code);
    $response->setBody(json_encode([$key=>$message]));

    HTTP\Sapi::sendResponse($response);

    exit($status_code >= 400 ? 1 : 0);
}

function return_error(HTTP\RequestInterface $request, HTTP\ResponseInterface $response, $str)
{
    json_response($request, $response, 400, 'error', $str);
}

function return_success($request, $response, $str)
{
    json_response($request, $response, 200, 'success', $str);
}
