<?php

use Sabre\HTTP;

require_once 'vendor/autoload.php';

function send_email(HTTP\RequestInterface $request, HTTP\ResponseInterface $response, $data)
{
    try {
        // Create the Transport
        $transport = (new Swift_SmtpTransport($data['smtp_server'], $data['smtp_port']))
            ->setUsername($data['smtp_user'])
            ->setPassword($data['smtp_password']);
    
        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);
    
        // Create a message
        $message = (new Swift_Message($data['subject']))
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setBody(strip_tags($data['message']))
            ->addPart($data['message'], 'text/html');

        // Send the message
        $result = $mailer->send($message);
        
        return_success($request, $response, true);
    } catch (\Throwable $th) {
        // show more detailed error log if DEBUG is true
        if ($_ENV['DEBUG']) {
            return_error($request, $response, $th->getMessage());
        } else {
            return_error($request, $response, 'Server problem while sending email.');
        }
    }
}
