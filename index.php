<?php

require_once 'vendor/autoload.php';

function return_error($str)
{
    http_response_code(400);
    die(json_encode(['error'=>$str]));
}
function return_success($str)
{
    http_response_code(200);
    die(json_encode(['success'=>$str]));
}

// load .env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required([
    "TOKEN",
    "FROM_EMAIL",
    "FROM_NAME",
    "SMTP_USER",
    "SMTP_PASSWORD",
    "SMTP_SERVER",
    "SMTP_PORT"
    ])->notEmpty();
    
$token = $_ENV["TOKEN"];
$from_email = $_ENV["FROM_EMAIL"];
$from_name = $_ENV["FROM_NAME"];
$smtp_user = $_ENV["SMTP_USER"];
$smtp_password = $_ENV["SMTP_PASSWORD"];
$smtp_server = $_ENV["SMTP_SERVER"];
$smtp_port = $_ENV["SMTP_PORT"];

// parse POST arguments
$required_post = ['subject','to','message', 'token'];
foreach ($required_post as $field) {
    if (!isset($_POST[$field])) {
        return_error("Field '${field}' is missing");
    }
    if (empty($_POST[$field])) {
        return_error("Field '${field}' is empty");
    }
}

// validate token
if ($token !== $_POST['token']) {
    return_error("Your token is invalid");
}

$subject = $_POST['subject'];
$to = isset($_POST['to_name']) && !empty($_POST['to_name']) ?
    [$_POST['to']=>$_POST['to_name']] :
    [$_POST['to']];
$from = isset($_POST['from_name']) && !empty($_POST['from_name']) ?
    [$from_email => $_POST['from_name'] ]:
    [$from_email => $from_name];
$message = $_POST['message'];
    
    // Create the Transport
$transport = (new Swift_SmtpTransport($smtp_server, $smtp_port))
  ->setUsername($smtp_user)
  ->setPassword($smtp_password)
;

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// Create a message
$message = (new Swift_Message($subject))
  ->setFrom($from)
  ->setTo($to)
  ->setBody($message)
  ;

// Send the message
$result = $mailer->send($message);


return_success(true);
