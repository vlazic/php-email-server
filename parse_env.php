<?php

use Sabre\HTTP;

require_once 'vendor/autoload.php';

function parse_env(HTTP\RequestInterface $request, HTTP\ResponseInterface $response)
{
    // load .env variables
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    try {
        $dotenv->required([
            "TOKEN",
            "FROM_EMAIL",
            "FROM_NAME",
            "SMTP_USER",
            "SMTP_PASSWORD",
            "SMTP_SERVER",
            "SMTP_ENCRYPTION",
            "SMTP_PORT",
        ])
            ->notEmpty();

        // parse 'DEBUG' env
        $dotenv->required('DEBUG')->isBoolean();
        $_ENV['DEBUG'] = $_ENV['DEBUG'] === 'On';
    } catch (\Throwable $th) {
        return_error($request, $response, $th->getMessage() . ' You need to include it in .env file on server.');
    }

    return [
        'token' => $_ENV["TOKEN"],
        'from_email' => $_ENV["FROM_EMAIL"],
        'from_name' => $_ENV["FROM_NAME"],
        'smtp_user' => $_ENV["SMTP_USER"],
        'smtp_password' => $_ENV["SMTP_PASSWORD"],
        'smtp_server' => $_ENV["SMTP_SERVER"],
        'smtp_encryption' => $_ENV["SMTP_ENCRYPTION"],
        'smtp_port' => $_ENV["SMTP_PORT"],
    ];
}
